<?php

use App\Domain\Catalog\Models\Course;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Cek kursus "pendampingan-umkm" (ID 6)
$course = Course::find(6);
if ($course) {
    echo 'Kursus: '.$course->slug.PHP_EOL;
    echo 'Requirement: '.($course->requirement ? 'Ada (ID '.$course->requirement->id.')' : 'TIDAK ADA').PHP_EOL;
    if ($course->requirement) {
        echo '  - prerequisite_course_id: '.($course->requirement->prerequisite_course_id ?? 'null').PHP_EOL;
        echo '  - deleted_at: '.($course->requirement->deleted_at ?? 'null').PHP_EOL;
    }
} else {
    echo 'Kursus tidak ditemukan'.PHP_EOL;
}
