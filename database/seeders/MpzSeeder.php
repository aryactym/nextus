<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Models\LessonProgress;
use App\Domain\Learning\Models\QuizAttempt;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MpzSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Organisasi ─────────────────────────────────────────────────
        $org = Organization::firstOrCreate(
            ['slug' => 'mpz-gerak-bareng'],
            [
                'name' => ['id' => 'MPZ Gerak Bareng'],
                'status' => 'active',
                'default_locale' => 'id',
            ]
        );

        // ── 2. Org Admin ──────────────────────────────────────────────────
        $orgAdmin = User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'admin@mpzgerakbareng.org'],
            [
                'name' => 'Admin MPZ',
                'password' => Hash::make('password'),
                'organization_id' => $org->id,
                'locale' => 'id',
                'nama_jabatan' => 'Koordinator Pelatihan',
                'kategori_jabatan' => 'Manajer',
            ]
        );
        $orgAdmin->syncRoles(['Org Admin']);

        // ── 3. 10 Peserta ─────────────────────────────────────────────────
        $learners = [
            ['Ahmad Fauzi',      'ahmad.fauzi@mpzgerakbareng.org',    'Staf Program',      'Officer'],
            ['Siti Rahmawati',   'siti.r@mpzgerakbareng.org',         'Staf Keuangan',     'Officer'],
            ['Budi Santoso',     'budi.s@mpzgerakbareng.org',         'Supervisor Program', 'Supervisor'],
            ['Dewi Kusuma',      'dewi.k@mpzgerakbareng.org',         'Relawan Senior',    'Officer'],
            ['Hendra Wijaya',    'hendra.w@mpzgerakbareng.org',       'Manajer Lapangan',  'Manajer'],
            ['Fatimah Azzahra',  'fatimah.a@mpzgerakbareng.org',      'Staf Komunikasi',   'Officer'],
            ['Rizky Pratama',    'rizky.p@mpzgerakbareng.org',        'Koordinator Relawan', 'Supervisor'],
            ['Nurul Hidayah',    'nurul.h@mpzgerakbareng.org',        'Analis Data',       'Officer'],
            ['Andi Kurniawan',   'andi.k@mpzgerakbareng.org',         'Staf IT',           'Officer'],
            ['Maya Sari',        'maya.s@mpzgerakbareng.org',         'Direktur Program',  'Direktur'],
        ];

        $users = [];
        foreach ($learners as $data) {
            [$name, $email, $jabatan, $kategori] = $data;
            $user = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'organization_id' => $org->id,
                    'locale' => 'id',
                    'nama_jabatan' => $jabatan,
                    'kategori_jabatan' => $kategori,
                    'nama_organisasi' => 'MPZ Gerak Bareng',
                ]
            );
            $user->syncRoles(['Learner']);
            $users[] = $user;
        }

        // ── 4. Ambil kursus yang ada ──────────────────────────────────────
        $courses = Course::withoutGlobalScopes()
            ->where('status', 'published')
            ->with(['modules.lessons.quiz.questions'])
            ->get();

        if ($courses->isEmpty()) {
            $this->command->warn('Tidak ada kursus published. Jalankan DemoSeeder dulu.');

            return;
        }

        // Setiap peserta mengikuti 4–8 kursus acak
        foreach ($users as $userIndex => $user) {
            $courseCount = rand(4, 8);
            $selectedCourses = $courses->shuffle()->take($courseCount);

            foreach ($selectedCourses as $courseIndex => $course) {
                // Cek sudah enrolled atau belum
                $existing = Enrollment::withoutGlobalScopes()
                    ->where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                // Variasikan progres: beberapa selesai, beberapa di tengah
                $scenarioRoll = ($userIndex + $courseIndex) % 5;
                $isCompleted = $scenarioRoll < 3; // ~60% selesai
                $progress = $isCompleted ? 100 : rand(20, 85);
                $enrolledAt = now()->subDays(rand(30, 180));
                $completedAt = $isCompleted ? $enrolledAt->copy()->addDays(rand(3, 21)) : null;

                $enrollment = Enrollment::withoutGlobalScopes()->create([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => 'active',
                    'progress_percent' => $progress,
                    'enrolled_at' => $enrolledAt,
                    'completed_at' => $completedAt,
                ]);

                // Seed lesson progress & quiz attempts
                $allLessons = $course->modules->flatMap(fn ($m) => $m->lessons);
                $completedLessons = $isCompleted
                    ? $allLessons
                    : $allLessons->take((int) ceil($allLessons->count() * $progress / 100));

                foreach ($completedLessons as $lesson) {
                    LessonProgress::withoutGlobalScopes()->firstOrCreate(
                        ['enrollment_id' => $enrollment->id, 'lesson_id' => $lesson->id],
                        ['completed_at' => $enrolledAt->copy()->addDays(rand(1, 15)), 'last_position' => 0]
                    );

                    // Quiz attempts
                    if ($lesson->type === 'quiz' && $lesson->quiz) {
                        $score = $isCompleted ? rand(70, 100) : rand(40, 85);
                        $passed = $score >= 70;

                        QuizAttempt::withoutGlobalScopes()->firstOrCreate(
                            ['enrollment_id' => $enrollment->id, 'quiz_id' => $lesson->quiz->id],
                            [
                                'uuid' => (string) Str::uuid(),
                                'score' => $score,
                                'passed' => $passed,
                                'answers' => [],
                                'finished_at' => $enrolledAt->copy()->addDays(rand(2, 16)),
                            ]
                        );
                    }
                }

                // Sertifikat untuk yang selesai
                if ($isCompleted) {
                    Certificate::withoutGlobalScopes()->firstOrCreate(
                        ['enrollment_id' => $enrollment->id],
                        [
                            'uuid' => (string) Str::uuid(),
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'code' => strtoupper(Str::random(12)),
                            'issued_at' => $completedAt,
                        ]
                    );
                }
            }
        }

        $this->command->info("MPZ Gerak Bareng seeder selesai: {$org->name}, ".count($users).' peserta.');
    }
}
