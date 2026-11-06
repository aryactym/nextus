<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Http\Requests\StoreCourseRequest;
use App\Domain\Catalog\Http\Requests\UpdateCourseRequest;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::forOrganization()->with('category')->latest()->paginate(10);

        return view('catalog.courses.index', compact('courses'));
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
        $course->load('modules.lessons');

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
