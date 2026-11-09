<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalog\Models\Course;
use App\Domain\Organization\Models\Organization;
use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::where('slug', 'yayasan-filantropi-islam')->first();

        if (! $org) {
            $this->command->warn('Jalankan DatabaseSeeder dulu.');

            return;
        }

        $courses = Course::withoutGlobalScopes()
            ->where('status', 'published')
            ->pluck('id', 'slug');

        $events = [
            [
                'title' => 'Webinar: Zakat dan Pemberdayaan Ekonomi',
                'type' => 'webinar',
                'format' => 'daring',
                'status' => 'open',
                'starts_at' => now()->addDays(6)->setTime(13, 0),
                'ends_at' => now()->addDays(6)->setTime(15, 0),
                'location' => 'Zoom',
                'quota' => 200,
                'registration_url' => null,
                'course_id' => $courses['pengantar-zakat'] ?? null,
                'description' => 'Membahas peran zakat sebagai instrumen pemberdayaan ekonomi umat.',
            ],
            [
                'title' => 'Dasar Fasilitasi (ToT) Angkatan 3',
                'type' => 'pelatihan',
                'format' => 'daring',
                'status' => 'open',
                'starts_at' => now()->addDays(13)->setTime(8, 30),
                'ends_at' => now()->addDays(13)->setTime(16, 0),
                'location' => 'Zoom',
                'quota' => 40,
                'registration_url' => null,
                'course_id' => $courses['dasar-fasilitasi-tot'] ?? null,
                'description' => 'Pelatihan dua hari intensif untuk fasilitator program filantropi.',
            ],
            [
                'title' => 'Pelatihan Safeguarding bagi Relawan',
                'type' => 'pelatihan',
                'format' => 'luring',
                'status' => 'open',
                'starts_at' => now()->addDays(20)->setTime(9, 0),
                'ends_at' => now()->addDays(20)->setTime(16, 0),
                'location' => 'Jakarta',
                'quota' => 30,
                'registration_url' => null,
                'course_id' => $courses['safeguarding-relawan'] ?? null,
                'description' => 'Pelatihan luring satu hari tentang prinsip perlindungan penerima manfaat.',
            ],
            [
                'title' => 'Batch Penyaluran Zakat Tepat Sasaran',
                'type' => 'batch',
                'format' => 'daring',
                'status' => 'open',
                'starts_at' => now()->addDays(27)->setTime(9, 0),
                'ends_at' => now()->addDays(27)->setTime(12, 0),
                'location' => 'Platform NextUs',
                'quota' => 100,
                'registration_url' => null,
                'course_id' => $courses['penyaluran-zakat'] ?? null,
                'description' => 'Batch kursus mandiri dengan fasilitator untuk diskusi kelompok.',
            ],
            [
                'title' => 'Webinar: Digitalisasi Lembaga Filantropi',
                'type' => 'webinar',
                'format' => 'daring',
                'status' => 'soon',
                'starts_at' => now()->addDays(34)->setTime(13, 0),
                'ends_at' => now()->addDays(34)->setTime(15, 0),
                'location' => 'Zoom',
                'quota' => 300,
                'registration_url' => null,
                'course_id' => $courses['digitalisasi-lembaga'] ?? null,
                'description' => 'Strategi dan tools untuk transformasi digital lembaga filantropi.',
            ],
            [
                'title' => 'Pelatihan Pendampingan UMKM Binaan',
                'type' => 'pelatihan',
                'format' => 'luring',
                'status' => 'soon',
                'starts_at' => now()->addDays(48)->setTime(9, 0),
                'ends_at' => now()->addDays(48)->setTime(16, 0),
                'location' => 'Bandung',
                'quota' => 25,
                'registration_url' => null,
                'course_id' => $courses['pendampingan-umkm'] ?? null,
                'description' => 'Pelatihan lapangan untuk pendamping UMKM binaan lembaga.',
            ],
            // Past events
            [
                'title' => 'Webinar: Akuntabilitas Pelaporan Program',
                'type' => 'webinar',
                'format' => 'daring',
                'status' => 'closed',
                'starts_at' => now()->subDays(10)->setTime(13, 0),
                'ends_at' => now()->subDays(10)->setTime(15, 0),
                'location' => 'Zoom',
                'quota' => 150,
                'registration_url' => null,
                'course_id' => $courses['dasar-pelaporan-program'] ?? null,
                'description' => 'Rekaman tersedia di platform bagi peserta terdaftar.',
            ],
            [
                'title' => 'Batch Pengantar Zakat untuk Relawan',
                'type' => 'batch',
                'format' => 'daring',
                'status' => 'closed',
                'starts_at' => now()->subDays(20)->setTime(9, 0),
                'ends_at' => now()->subDays(20)->setTime(12, 0),
                'location' => 'Platform NextUs',
                'quota' => 80,
                'registration_url' => null,
                'course_id' => $courses['pengantar-zakat'] ?? null,
                'description' => null,
            ],
        ];

        foreach ($events as $data) {
            Event::withoutGlobalScopes()->firstOrCreate(
                ['title' => $data['title'], 'organization_id' => $org->id],
                array_merge($data, ['organization_id' => $org->id])
            );
        }

        $this->command->info('EventSeeder: '.count($events).' jadwal dibuat.');
    }
}
