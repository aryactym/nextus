<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Learning\Mail\CourseReminder;
use App\Domain\Learning\Models\Enrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCourseReminders extends Command
{
    protected $signature = 'reminders:courses';

    protected $description = 'Kirim pengingat kursus yang belum selesai ke learner.';

    public function handle(): int
    {
        $enrollments = Enrollment::query()
            ->with(['user', 'course'])
            ->where('status', 'active')
            ->where('progress_percent', '<', 100)
            ->get();

        $sent = 0;

        foreach ($enrollments as $enrollment) {
            if (! $enrollment->user || ! $enrollment->course) {
                continue;
            }

            Mail::to($enrollment->user)->send(new CourseReminder($enrollment));
            $sent++;
        }

        $this->info("Pengingat terkirim: {$sent}");

        return self::SUCCESS;
    }
}
