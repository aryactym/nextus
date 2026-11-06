<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Http\Requests\StoreModuleRequest;
use App\Domain\Catalog\Http\Requests\UpdateModuleRequest;
use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseModule;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class ModuleController extends Controller
{
    public function store(StoreModuleRequest $request, Course $course)
    {
        $course = Course::forOrganization()->findOrFail($course->getKey());
        $data = $request->validated();
        $data['sort'] = $data['sort'] ?? $course->modules()->max('sort') + 1;
        $course->modules()->create($data);

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Modul berhasil ditambahkan.');
    }

    public function update(UpdateModuleRequest $request, CourseModule $module): RedirectResponse
    {
        $module = CourseModule::query()
            ->whereHas('course', fn (Builder $query) => $query->forOrganization())
            ->findOrFail($module->getKey());
        $module->update($request->validated());

        return redirect()->route('admin.courses.edit', $module->course)
            ->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(CourseModule $module)
    {
        abort_unless(auth()->user()->can('manage courses'), 403);
        $module = CourseModule::query()
            ->whereHas('course', fn (Builder $query) => $query->forOrganization())
            ->findOrFail($module->getKey());
        $course = Course::forOrganization()->findOrFail($module->course_id);
        $module->delete();

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Modul berhasil dihapus.');
    }
}
