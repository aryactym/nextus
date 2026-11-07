<?php

declare(strict_types=1);

namespace App\Domain\Learning\Services;

use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Models\LessonProgress;

class CourseProgressService
{
    public function __construct(private CertificateIssuer $certificates) {}

    /**
     * Sinkronkan progres enrollment, lalu terbitkan sertifikat bila selesai.
     */
    public function sync(Enrollment $enrollment): Enrollment
    {
        $enrollment->loadMissing('course.modules.lessons');

        $lessons = $enrollment->course->modules->flatMap->lessons;
        $total = $lessons->count();
        $completedIds = $enrollment->lessonProgress()
            ->whereNotNull('completed_at')
            ->pluck('lesson_id');
        $done = $lessons->whereIn('id', $completedIds)->count();
        $isComplete = $total > 0 && $done >= $total;

        $enrollment->update([
            'progress_percent' => $total > 0 ? (int) round($done / $total * 100) : 0,
            'status' => $isComplete ? 'completed' : 'active',
            'completed_at' => $isComplete ? ($enrollment->completed_at ?? now()) : null,
        ]);

        if ($isComplete) {
            $this->certificates->issueIfEligible($enrollment->refresh());
        }

        return $enrollment;
    }

    public function markLessonCompleted(Enrollment $enrollment, Lesson $lesson): Enrollment
    {
        LessonProgress::updateOrCreate(
            ['enrollment_id' => $enrollment->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()],
        );

        return $this->sync($enrollment);
    }
}
