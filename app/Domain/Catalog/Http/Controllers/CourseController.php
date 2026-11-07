<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Http\Requests\StoreCourseRequest;
use App\Domain\Catalog\Http\Requests\UpdateCourseRequest;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
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
        $categories = Category::forOrganization()->orderBy('sort')->get();

        return view('catalog.courses.create', compact('categories'));
    }

    public function store(StoreCourseRequest $request, UploadService $upload)
    {
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

        Course::create($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil ditambahkan.');
    }

    public function edit(Course $course)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);
        $categories = Category::forOrganization()->orderBy('sort')->get();
        $course->load([
            'modules.lessons' => fn ($query) => $query->orderBy('sort'),
            'modules.lessons.quiz.questions',
        ]);

        return view('catalog.courses.edit', compact('course', 'categories'));
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

        $course->update($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);
        if ($course->thumbnail) {
            (new UploadService)->delete($course->thumbnail);
        }
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil dihapus.');
    }
}
