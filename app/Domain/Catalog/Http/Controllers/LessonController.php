<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Http\Requests\StoreLessonRequest;
use App\Domain\Catalog\Http\Requests\UpdateLessonRequest;
use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class LessonController extends Controller
{
    public function store(StoreLessonRequest $request, CourseModule $module, UploadService $upload)
    {
        $module = CourseModule::query()
            ->whereHas('course', fn (Builder $query) => $query->forOrganization())
            ->findOrFail($module->getKey());
        $data = $request->validated();
        $data['sort'] = $data['sort'] ?? $module->lessons()->max('sort') + 1;
        $data['is_preview'] = $data['is_preview'] ?? false;

        if ($request->hasFile('file')) {
            $result = $upload->upload($request->file('file'), 'document', 'lessons/'.$module->id);
            $data['file_path'] = $result['path'];
        }

        $module->lessons()->create($data);

        return redirect()->route('admin.courses.edit', $module->course)
            ->with('success', 'Pelajaran berhasil ditambahkan.');
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson, UploadService $upload): RedirectResponse
    {
        $lesson = Lesson::query()
            ->whereHas('module.course', fn (Builder $query) => $query->forOrganization())
            ->findOrFail($lesson->getKey());
        $data = $request->validated();
        $data['is_preview'] = $request->boolean('is_preview');

        if ($request->hasFile('file')) {
            if ($lesson->file_path) {
                $upload->delete($lesson->file_path);
            }
            $result = $upload->upload($request->file('file'), 'document', 'lessons/'.$lesson->module_id);
            $data['file_path'] = $result['path'];
        }

        $lesson->update($data);

        if ($lesson->type !== 'quiz') {
            $lesson->quiz()->delete();
        }

        return redirect()->route('admin.courses.edit', $lesson->module->course)
            ->with('success', 'Pelajaran berhasil diperbarui.');
    }

    public function destroy(Lesson $lesson, UploadService $upload)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);
        $lesson = Lesson::query()
            ->whereHas('module.course', fn (Builder $query) => $query->forOrganization())
            ->findOrFail($lesson->getKey());
        $course = $lesson->module->course;

        if ($lesson->file_path) {
            $upload->delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Pelajaran berhasil dihapus.');
    }
}
