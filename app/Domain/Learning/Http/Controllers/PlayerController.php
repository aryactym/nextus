<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Models\LessonProgress;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function show(Request $request, string $courseSlug, Lesson $lesson): View|RedirectResponse
    {
        $course = Course::withoutGlobalScope('organization')
            ->where('slug', $courseSlug)
            ->where('status', 'published')
            ->with(['category', 'modules.lessons' => fn ($q) => $q->orderBy('sort')])
            ->firstOrFail();

        $lessonIds = $course->modules->flatMap->lessons->pluck('id');
        if (! $lessonIds->contains($lesson->id)) {
            abort(404);
        }

        $enrollment = $request->user()?->enrollments()
            ->where('course_id', $course->id)->first();

        if (! $lesson->is_preview && ! $enrollment) {
            return $request->user() === null
                ? redirect()->route('login')
                : redirect()->route('catalog.show', $course->slug);
        }

        $completedLessonIds = $enrollment
            ? $enrollment->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id')
            : collect();

        /** @var Lesson|null $previousLesson */
        $previousLesson = $this->neighborLesson($course, $lesson, -1);
        /** @var Lesson|null $nextLesson */
        $nextLesson = $this->neighborLesson($course, $lesson, 1);

        $fileUrl = ($lesson->file_path && $request->user())
            ? URL::signedRoute('admin.uploads.show', ['path' => $lesson->file_path])
            : null;

        $totalLessons = $course->modules->flatMap->lessons->count();

        return view('learning.player', compact(
            'course', 'lesson', 'enrollment', 'completedLessonIds',
            'previousLesson', 'nextLesson', 'fileUrl', 'totalLessons'
        ));
    }

    public function complete(Request $request, string $courseSlug, Lesson $lesson): RedirectResponse
    {
        $course = Course::withoutGlobalScope('organization')
            ->where('slug', $courseSlug)
            ->where('status', 'published')
            ->with(['modules.lessons'])
            ->firstOrFail();

        $enrollment = $request->user()->enrollments()
            ->where('course_id', $course->id)->firstOrFail();

        LessonProgress::updateOrCreate(
            ['enrollment_id' => $enrollment->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()],
        );

        $total = $course->modules->flatMap->lessons->count();
        $done = $enrollment->lessonProgress()->whereNotNull('completed_at')->count();
        $enrollment->update([
            'progress_percent' => $total > 0 ? (int) round($done / $total * 100) : 0,
            'status' => $total > 0 && $done >= $total ? 'completed' : 'active',
            'completed_at' => $total > 0 && $done >= $total ? now() : null,
        ]);

        $next = $this->nextIncompleteLesson($course, $lesson, $enrollment);

        return redirect()->route(
            'player.show',
            ['course' => $course->slug, 'lesson' => $next?->uuid ?? $lesson->uuid]
        );
    }

    private function neighborLesson(Course $course, Lesson $current, int $offset): ?Lesson
    {
        $ordered = $course->modules->flatMap->lessons->values();
        $index = $ordered->search(fn ($l) => $l->id === $current->id);
        $target = $index === false ? null : ($ordered[$index + $offset] ?? null);

        return $target instanceof Lesson ? $target : null;
    }

    private function nextIncompleteLesson(Course $course, Lesson $current, Enrollment $enrollment): ?Lesson
    {
        $completed = $enrollment->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id');
        $ordered = $course->modules->flatMap->lessons->values();
        $index = $ordered->search(fn ($l) => $l->id === $current->id);

        return $ordered
            ->slice($index === false ? 0 : $index + 1)
            ->first(fn ($l) => ! $completed->contains($l->id));
    }
}
