<?php

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseRequirement;

// UUID kursus: 464f2e8d-739d-4fb7-9ef7-fd8b23340c68
$course = Course::where('uuid', '464f2e8d-739d-4fb7-9ef7-fd8b23340c68')->first();

if (! $course) {
    echo "Kursus tidak ditemukan\n";

    return;
}

echo 'Kursus: '.$course->getTranslation('title', 'id')."\n";
echo 'Requirement saat ini: '.($course->requirement ? 'Ada' : 'Tidak ada')."\n";

// Dari git history, restore requirement yang sebelumnya ada
// Cek commit 5814861 atau lebih awal untuk syarat
// Jika tidak ada record, bikin baru sesuai requirement form terakhir

// Untuk restore, kita ambil dari history git atau manual input
// Kali ini, kita buat requirement placeholder dulu
// User bisa re-edit dengan nilai yang benar

if (! $course->requirement) {
    $requirement = CourseRequirement::create([
        'course_id' => $course->id,
        'prerequisite_course_id' => null,
        'min_grade' => null,
        'pendidikan_id' => null,
        'min_age' => null,
        'max_age' => null,
        'notes' => 'Restored from backup',
    ]);
    echo "Requirement berhasil di-restore (placeholder)\n";
    echo "Edit kembali kursus untuk mengisi syarat yang benar\n";
} else {
    echo "Requirement sudah ada, tidak perlu restore\n";
}
