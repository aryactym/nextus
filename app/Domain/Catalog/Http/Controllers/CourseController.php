<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Http\Requests\StoreCourseRequest;
use App\Domain\Catalog\Http\Requests\UpdateCourseRequest;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
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
            'level' => (string) $request->input('level', ''),
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
            ->when($filters['level'] !== '', fn ($query) => $query->where('level', $filters['level']))
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
        $data['is_featured'] = $data['is_featured'] ?? false;
        $data['featured_sort'] = $data['featured_sort'] ?? 0;

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

    public function show(Course $course)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);

        $course->load([
            'category',
            'modules.lessons',
            'enrollments' => fn ($q) => $q->with(['user', 'certificate', 'order'])->latest('enrolled_at'),
        ]);

        $enrolledCount = $course->enrollments->count();
        $completedCount = $course->enrollments->whereNotNull('completed_at')->count();
        $certificateCount = $course->enrollments->filter(fn ($e) => $e->certificate !== null)->count();
        $moduleCount = $course->modules->count();
        $lessonCount = $course->modules->sum(fn ($m) => $m->lessons->count());
        $revenue = $course->enrollments
            ->filter(fn ($e) => $e->order && $e->order->status === 'paid')
            ->sum(fn ($e) => $e->order->amount);
        $avgProgress = $enrolledCount > 0
            ? (int) round($course->enrollments->avg('progress_percent'))
            : 0;

        return view('catalog.courses.show', compact(
            'course', 'enrolledCount', 'completedCount', 'certificateCount',
            'moduleCount', 'lessonCount', 'revenue', 'avgProgress'
        ));
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

        \Log::info('CourseController.update START', [
            'course_id' => $course->id,
            'course_title_before' => $course->title,
        ]);

        $data['suggested_amounts'] = $data['suggested_amounts'] ?? null;
        $data['allow_custom_amount'] = $data['allow_custom_amount'] ?? true;
        $data['currency'] = $data['currency'] ?? 'IDR';
        $data['passing_grade'] = $data['passing_grade'] ?? 75;
        $data['is_featured'] = $request->boolean('is_featured');
        $data['featured_sort'] = $data['featured_sort'] ?? $course->featured_sort ?? 0;

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

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    /**
     * Simpan atau hapus syarat kelulusan kursus.
     * DIHANDLE DI ENDPOINT TERPISAH, bukan di course update
     */
    private function saveRequirement(Course $course, Request $request): void
    {
        // SKIP sepenuhnya — requirement dihandle via separate endpoint
        // Jangan sentuh requirement saat course update

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
