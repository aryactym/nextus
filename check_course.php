<?php

use App\Domain\Catalog\Models\Course;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$course = Course::where('slug', 'kesiapsiagaan-bencana-komunitas')->first();
if ($course) {
    echo 'Kursus: '.$course->slug.PHP_EOL;
    echo 'ID: '.$course->id.PHP_EOL;
    echo 'UUID: '.$course->uuid.PHP_EOL;
    echo 'Requirement: '.($course->requirement ? 'Ada' : 'Tidak ada').PHP_EOL;
    if ($course->requirement) {
        echo 'Prerequisite: '.$course->requirement->prerequisite_course_id.PHP_EOL;
        echo 'Min grade: '.$course->requirement->min_grade.PHP_EOL;
        echo 'Pendidikan: '.$course->requirement->pendidikan_id.PHP_EOL;
    }
} else {
    echo 'Kursus tidak ditemukan'.PHP_EOL;
}
