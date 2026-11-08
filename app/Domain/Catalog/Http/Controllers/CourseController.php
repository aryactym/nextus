<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Http\Requests\StoreCourseRequest;
use App\Domain\Catalog\Http\Requests\UpdateCourseRequest;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseRequirement;
use App\Domain\Catalog\Models\Pendidikan;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);

        $filters = [
            'q' => trim((string) $request->input('q', '')),
            'status' => (string) $request->input('status', ''),
            'access_type' => (string) $request->input('access_type', ''),
            'category' => (string) $request->input('category', ''),
        ];

        $categories = Category::forOrganization()->orderBy('sort')->get();

        $courses = Course::forOrganization()
            ->with('category')
            ->when($filters['q'] !== '', function ($query) use ($filters): void {
                $query->where(function ($query) use ($filters): void {
                    $query->where('title->id', 'like', '%'.$filters['q'].'%')
                        ->orWhere('title->en', 'like', '%'.$filters['q'].'%')
                        ->orWhere('slug', 'like', '%'.$filters['q'].'%');
                });
            })
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['access_type'] !== '', fn ($query) => $query->where('access_type', $filters['access_type']))
            ->when($filters['category'] !== '', fn ($query) => $query->where('category_id', $filters['category']))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('catalog.courses.index', compact('courses', 'categories', 'filters'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('manage courses'), 403);

        $categories = Category::forOrganization()->orderBy('sort')->get();

        return view('catalog.courses.create', compact('categories'));
    }

    public function store(StoreCourseRequest $request, UploadService $upload)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);

        $data = $request->validated();
        $data['organization_id'] = organization_id();
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']['id'] ?? 'course');
        $data['suggested_amounts'] = $data['suggested_amounts'] ?? null;
        $data['allow_custom_amount'] = $data['allow_custom_amount'] ?? true;
        $data['currency'] = $data['currency'] ?? 'IDR';
        $data['passing_grade'] = $data['passing_grade'] ?? 75;

        if ($request->hasFile('thumbnail')) {
            $result = $upload->upload($request->file('thumbnail'), 'image', 'courses/thumbnails');
            $data['thumbnail'] = $result['path'];
        }

        if ($request->hasFile('certificate_background')) {
            $result = $upload->upload($request->file('certificate_background'), 'image', 'courses/certificates');
            $data['certificate_background'] = $result['path'];
        }

        Course::create($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil ditambahkan.');
    }

    public function edit(Course $course)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);
        $categories = Category::forOrganization()->orderBy('sort')->get();
        $pendidikans = Pendidikan::orderBy('level')->get();
        $course->load([
            'modules.lessons' => fn ($query) => $query->orderBy('sort'),
            'modules.lessons.quiz.questions',
            'requirement' => fn ($q) => $q->with(['prerequisiteCourse', 'pendidikan']),
        ]);

        return view('catalog.courses.edit', compact('course', 'categories', 'pendidikans'));
    }

    public function update(UpdateCourseRequest $request, Course $course, UploadService $upload)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);
        $data = $request->validated();
        
        $data['suggested_amounts'] = $data['suggested_amounts'] ?? null;
        $data['allow_custom_amount'] = $data['allow_custom_amount'] ?? true;
        $data['currency'] = $data['currency'] ?? 'IDR';
        $data['passing_grade'] = $data['passing_grade'] ?? 75;

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                (new UploadService)->delete($course->thumbnail);
            }
            $result = $upload->upload($request->file('thumbnail'), 'image', 'courses/thumbnails');
            $data['thumbnail'] = $result['path'];
        }

        if ($request->hasFile('certificate_background')) {
            if ($course->certificate_background) {
                (new UploadService)->delete($course->certificate_background);
            }
            $result = $upload->upload($request->file('certificate_background'), 'image', 'courses/certificates');
            $data['certificate_background'] = $result['path'];
        }

        $course->update($data);

        // Syarat kelulusan (requirement)
        $this->saveRequirement($course, $request);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    private function saveRequirement(Course $course, Request $request): void
    {
        $clear = $request->boolean('clear_requirement');

        if ($clear) {
            try {
                $course->requirement?->forceDelete();
            } catch (\Exception $e) {
                \Log::warning('Gagal hapus requirement', ['error' => $e->getMessage()]);
            }
            return;
        }

        // Jika form tidak dikirim requirement field sama sekali, jangan ubah
        // Cek apakah ada field requirement yang dikirim
        $hasRequirementFields = $request->has('prerequisite_course_id') 
            || $request->has('min_grade') 
            || $request->has('pendidikan_id') 
            || $request->has('min_age') 
            || $request->has('max_age') 
            || $request->has('notes');

        if (!$hasRequirementFields) {
            return; // Form tidak kirim requirement field, skip
        }

        $data = $request->only([
            'prerequisite_course_id', 'min_grade', 'pendidikan_id', 'min_age', 'max_age', 'notes',
        ]);

        if ((int) ($data['prerequisite_course_id'] ?? 0) === $course->id) {
            $data['prerequisite_course_id'] = null;
        }

        foreach (['prerequisite_course_id', 'min_grade', 'pendidikan_id', 'min_age', 'max_age'] as $field) {
            if (isset($data[$field]) && ($data[$field] === '' || $data[$field] === '0')) {
                $data[$field] = null;
            }
        }

        $data['course_id'] = $course->id;

        CourseRequirement::updateOrCreate(
            ['course_id' => $course->id],
            $data,
        );
    }

    public function destroy(Course $course)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);
        if ($course->thumbnail) {
            (new UploadService)->delete($course->thumbnail);
        }
        if ($course->certificate_background) {
            (new UploadService)->delete($course->certificate_background);
        }
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil dihapus.');
    }
}
