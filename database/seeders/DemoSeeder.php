<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
use App\Domain\Organization\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('slug', 'yayasan-filantropi-islam')->first()
            ?? Organization::create(['name' => 'Yayasan Filantropi Islam', 'slug' => 'yayasan-filantropi-islam', 'status' => 'active']);

        $categories = collect([
            ['dasar-ziswaf', 'Dasar Ziswaf', 'Zakat, infak, sedekah, wakaf'],
            ['pengelolaan-zakat', 'Pengelolaan Zakat', 'Menghimpun dan menyalurkan zakat'],
            ['pemberdayaan', 'Pemberdayaan Masyarakat', 'Program pemberdayaan'],
            ['tata-kelola', 'Tata Kelola & Kepatuhan', 'Pelaporan dan kepatuhan'],
            ['manajemen-lembaga', 'Manajemen Lembaga', 'Mengelola lembaga filantropi'],
            ['relawan-kebencanaan', 'Relawan & Kebencanaan', 'Relawan dan tanggap bencana'],
        ])->mapWithKeys(fn ($row) => [$row[0] => Category::firstOrCreate(
            ['slug' => $row[0], 'organization_id' => $organization->id],
            ['name' => ['id' => $row[1]], 'sort' => 1],
        )]);

        $courses = [
            ['Pengantar Zakat untuk Relawan', 'pengantar-zakat', 'dasar-ziswaf', 'beginner', 120, 'free', 'free', 'Dasar zakat dan peran relawan dalam penghimpunan.'],
            ['Dasar Pelaporan Program', 'dasar-pelaporan-program', 'tata-kelola', 'intermediate', 240, 'donation', 'free', 'Menyusun laporan program yang akuntabel.'],
            ['Dasar Fasilitasi (ToT)', 'dasar-fasilitasi-tot', 'manajemen-lembaga', 'intermediate', 360, 'free', 'donation', 'Prasyarat untuk mengelola pelatihan sebagai penyelenggara.'],
            ['Wakaf dan Sedekah untuk Pemula', 'wakaf-sedekah-pemula', 'dasar-ziswaf', 'beginner', 120, 'free', 'free', 'Perbedaan zakat, infak, sedekah, dan wakaf.'],
            ['Penyaluran Zakat Tepat Sasaran', 'penyaluran-zakat', 'pengelolaan-zakat', 'intermediate', 300, 'donation', 'free', 'Menyeleksi dan menyalurkan zakat kepada penerima manfaat.'],
            ['Pendampingan UMKM Binaan', 'pendampingan-umkm', 'pemberdayaan', 'advanced', 480, 'donation', 'donation', 'Merancang program pemberdayaan ekonomi yang berkelanjutan.'],
            ['Safeguarding bagi Relawan', 'safeguarding-relawan', 'relawan-kebencanaan', 'beginner', 180, 'free', 'free', 'Melindungi penerima manfaat dan relawan di lapangan.'],
            ['Respon Kebencanaan Dasar', 'respon-kebencanaan', 'relawan-kebencanaan', 'intermediate', 240, 'free', 'free', 'Tahapan respon awal dan koordinasi dengan pihak terkait.'],
            ['Digitalisasi Lembaga Filantropi', 'digitalisasi-lembaga', 'manajemen-lembaga', 'advanced', 360, 'donation', 'donation', 'Memanfaatkan teknologi untuk layanan dan pelaporan.'],
            ['Manajemen Risiko Program Filantropi', 'manajemen-risiko-program', 'tata-kelola', 'intermediate', 300, 'donation', 'donation', 'Mengidentifikasi dan memitigasi risiko program.'],
            ['Komunikasi Publik Lembaga', 'komunikasi-publik-lembaga', 'manajemen-lembaga', 'beginner', 150, 'free', 'free', 'Menyampaikan pesan lembaga secara jelas dan kredibel.'],
            ['Audit Internal Sederhana', 'audit-internal-sederhana', 'tata-kelola', 'advanced', 420, 'paid', 'paid', 'Memeriksa kepatuhan dan mutu program secara mandiri.'],
            ['Zakat Perusahaan dan Kepatuhan', 'zakat-perusahaan-kepatuhan', 'pengelolaan-zakat', 'advanced', 330, 'donation', 'donation', 'Mengelola zakat perusahaan sesuai ketentuan.'],
            ['Pemetaan Sosial Ekonomi Mustahik', 'pemetaan-mustahik', 'pemberdayaan', 'intermediate', 240, 'free', 'free', 'Mengumpulkan dan membaca data penerima manfaat.'],
            ['Kesiapsiagaan Bencana Berbasis Komunitas', 'kesiapsiagaan-bencana-komunitas', 'relawan-kebencanaan', 'advanced', 360, 'donation', 'donation', 'Menyusun rencana kesiapsiagaan bersama warga.'],
        ];

        foreach ($courses as $index => $row) {
            [$title, $slug, $categorySlug, $level, $minutes, $access, $cert, $summary] = $row;

            $course = Course::withoutGlobalScope('organization')->firstOrCreate(
                ['slug' => $slug, 'organization_id' => $organization->id],
                [
                    'category_id' => $categories[$categorySlug]->id,
                    'title' => ['id' => $title],
                    'summary' => ['id' => $summary],
                    'description' => ['id' => $summary."\n\nKursus ini disusun agar peserta dapat memahami materi secara bertahap, lalu mempraktikkannya di lapangan. Setiap modul dilengkapi bacaan singkat, video, dan kuis untuk mengukur pemahaman."],
                    'level' => $level,
                    'duration_minutes' => $minutes,
                    'access_type' => $access,
                    'certificate_type' => $cert,
                    'status' => 'published',
                    'published_at' => now()->subDays(count($courses) - $index),
                    'visibility' => 'public',
                ]
            );

            if (! $course->thumbnail) {
                $source = public_path('images/course-'.(($index % 3) + 1).'.webp');
                if (File::exists($source)) {
                    $path = "courses/thumbnails/{$slug}.webp";
                    Storage::disk('uploads')->put($path, File::get($source));
                    $course->update(['thumbnail' => $path]);
                }
            }

            $outline = [
                [
                    'title' => 'Konsep dasar',
                    'lessons' => [
                        ['title' => 'Mengapa topik ini penting', 'type' => 'video', 'duration' => 10, 'is_preview' => true, 'content' => $summary],
                        ['title' => 'Istilah dan kerangka dasar', 'type' => 'text', 'duration' => 15, 'is_preview' => false, 'content' => 'Mengenal istilah utama, prinsip, dan kerangka kerja yang dipakai sepanjang kursus.'],
                        ['title' => 'Kuis singkat modul 1', 'type' => 'quiz', 'duration' => 5, 'is_preview' => false, 'content' => 'Uji pemahaman dasar sebelum melanjutkan ke praktik.'],
                    ],
                ],
                [
                    'title' => 'Praktik di lapangan',
                    'lessons' => [
                        ['title' => 'Langkah praktis di lapangan', 'type' => 'text', 'duration' => 15, 'is_preview' => false, 'content' => 'Panduan langkah demi langkah untuk menerapkan materi dalam kegiatan sehari-hari.'],
                        ['title' => 'Studi kasus dan diskusi', 'type' => 'video', 'duration' => 20, 'is_preview' => false, 'content' => 'Contoh situasi nyata, keputusan yang diambil, dan pelajaran dari lapangan.'],
                        ['title' => 'Kuis singkat modul 2', 'type' => 'quiz', 'duration' => 5, 'is_preview' => false, 'content' => 'Latihan menerapkan konsep pada situasi baru.'],
                    ],
                ],
                [
                    'title' => 'Amanah dan tindak lanjut',
                    'lessons' => [
                        ['title' => 'Prinsip amanah dan transparansi', 'type' => 'text', 'duration' => 12, 'is_preview' => false, 'content' => 'Menjaga kepercayaan donatur dan penerima manfaat melalui pencatatan yang jujur.'],
                        ['title' => 'Rencana tindak lanjut', 'type' => 'text', 'duration' => 10, 'is_preview' => false, 'content' => 'Menyusun rencana sederhana yang bisa langsung dijalankan setelah kursus.'],
                        ['title' => 'Asesmen akhir', 'type' => 'quiz', 'duration' => 15, 'is_preview' => false, 'content' => 'Asesmen akhir untuk mengukur pemahaman dan syarat sertifikat.'],
                    ],
                ],
            ];

            foreach ($outline as $moduleIndex => $moduleData) {
                $module = $course->modules()->updateOrCreate(
                    ['sort' => $moduleIndex + 1],
                    ['title' => ['id' => $moduleData['title']]],
                );

                foreach ($moduleData['lessons'] as $lessonIndex => $lessonData) {
                    $module->lessons()->updateOrCreate(
                        ['sort' => $lessonIndex + 1],
                        [
                            'title' => ['id' => $lessonData['title']],
                            'type' => $lessonData['type'],
                            'content' => ['id' => $lessonData['content']],
                            'duration' => $lessonData['duration'],
                            'is_preview' => $lessonData['is_preview'],
                        ],
                    );
                }
            }
        }
    }
}
