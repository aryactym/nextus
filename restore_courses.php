<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$course = \App\Domain\Catalog\Models\Course::withTrashed()->find(15);
if ($course && $course->deleted_at) {
  $course->restore();
  echo 'Kursus ' . $course->slug . ' sudah di-restore' . PHP_EOL;
} else {
  echo 'Kursus tidak ditemukan atau belum dihapus' . PHP_EOL;
}

// Restore juga ID 6 dan 8
$course6 = \App\Domain\Catalog\Models\Course::withTrashed()->find(6);
if ($course6 && $course6->deleted_at) {
  $course6->restore();
  echo 'Kursus ' . $course6->slug . ' sudah di-restore' . PHP_EOL;
}

$course8 = \App\Domain\Catalog\Models\Course::withTrashed()->find(8);
if ($course8 && $course8->deleted_at) {
  $course8->restore();
  echo 'Kursus ' . $course8->slug . ' sudah di-restore' . PHP_EOL;
}
