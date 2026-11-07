<?php

use App\Console\Commands\SendCourseReminders;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendCourseReminders::class)->dailyAt('09:00');
