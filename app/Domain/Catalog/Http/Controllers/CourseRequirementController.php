<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseRequirement;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseRequirementController extends Controller
{
    /**
     * Update requirement untuk course
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage courses'), 403);

        $clear = $request->boolean('clear_requirement');

        if ($clear) {
            try {
                $course->requirement?->forceDelete();
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal hapus syarat: '.$e->getMessage());
            }

            return back()->with('success', 'Syarat kelulusan dihapus.');
        }

        $data = $request->validate([
            'prerequisite_course_id' => ['nullable', 'exists:courses,id'],
            'prerequisite_category_id' => ['nullable', 'exists:categories,id'],
            'min_grade' => ['nullable', 'integer', 'min:0', 'max:100'],
            'pendidikan_id' => ['nullable', 'exists:pendidikans,id'],
            'min_age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'max_age' => ['nullable', 'integer', 'min:1', 'max:150', 'gte:min_age'],
            'notes' => ['nullable', 'string'],
        ]);

        // Validasi: prerequisite tidak boleh sama dengan course itu sendiri
        if ((int) ($data['prerequisite_course_id'] ?? 0) === $course->id) {
            $data['prerequisite_course_id'] = null;
        }

        $data['course_id'] = $course->id;

        CourseRequirement::updateOrCreate(
            ['course_id' => $course->id],
            $data,
        );

        return back()->with('success', 'Syarat kelulusan berhasil disimpan.');
    }
}
