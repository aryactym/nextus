<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Admin\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(
            ['slug' => 'syarat'],
            [
                'title' => 'Syarat dan ketentuan',
                'meta_description' => 'Aturan penggunaan NextUs. Mohon dibaca sebelum membuat akun dan mengikuti kursus.',
                'is_published' => true,
                'content' => <<<'HTML'
<blockquote>
  <p><strong>Draf template.</strong> Teks bertanda [...] harus diisi atau disesuaikan dengan praktik sebenarnya. Minta penasihat hukum meninjau dokumen ini sebelum dipublikasikan, lalu hapus kotak ini.</p>
</blockquote>

<h2>1. Penerimaan syarat</h2>
<p>Dengan membuat akun atau menggunakan NextUs ("Layanan"), kamu menyatakan telah membaca, memahami, dan menyetujui Syarat dan Ketentuan ini serta <a href="/privasi">Kebijakan Privasi</a>. Jika tidak setuju, mohon tidak menggunakan Layanan. Layanan dikelola oleh [nama badan hukum] ("kami").</p>

<h2>2. Definisi</h2>
<p><strong>Pengguna:</strong> setiap orang yang mengakses atau memakai Layanan. <strong>Akun:</strong> akun pribadi untuk mengikuti kursus. <strong>Kursus:</strong> materi pembelajaran daring beserta kuis dan penilaiannya. <strong>Sertifikat:</strong> bukti penyelesaian kursus berbentuk digital dengan kode verifikasi. <strong>Kegiatan:</strong> webinar, pelatihan, atau batch kursus pada halaman <a href="/jadwal">Jadwal</a>.</p>

<h2>3. Akun dan keamanan</h2>
<p>Kamu bertanggung jawab atas kerahasiaan kata sandi dan seluruh aktivitas pada akunmu. Segera beri tahu kami jika terjadi penggunaan tanpa izin.</p>

<h2>4. Penggunaan yang diperbolehkan</h2>
<p>Gunakan Layanan untuk tujuan pembelajaran yang sah. Dilarang menyalin, menjual, atau menyebarluaskan materi kursus tanpa izin tertulis.</p>

<h2>5. Kursus, akses, dan donasi</h2>
<p>Setiap kursus dapat berakses gratis, via donasi, atau berbayar sesuai ketentuan pada halaman kursus. Donasi dan pembayaran diproses melalui Midtrans; akses dibuka setelah pembayaran berstatus lunas.</p>

<h2>6. Sertifikat dan verifikasi</h2>
<p>Sertifikat diterbitkan atas nama peserta berdasarkan penyelesaian kursus. Dilarang memalsukan atau mengubah isi sertifikat. Keaslian dapat diperiksa pada halaman <a href="/verifikasi">Verifikasi</a>.</p>

<h2>7. Hak kekayaan intelektual</h2>
<p>Seluruh materi, logo, dan konten Layanan dilindungi hukum dan menjadi milik kami atau pemberi lisensinya.</p>

<h2>8. Konten dari Pengguna</h2>
<p>Konten yang kamu unggah menjadi tanggung jawabmu dan tidak boleh melanggar hak pihak lain atau hukum yang berlaku.</p>

<h2>9. Penafian dan batasan tanggung jawab</h2>
<p>Layanan disediakan "sebagaimana adanya". Kami tidak bertanggung jawab atas kerugian yang timbul dari penggunaan Layanan, sejauh diizinkan hukum.</p>

<h2>10. Penangguhan dan penghentian</h2>
<p>Kami dapat menangguhkan atau menghentikan akun yang melanggar Syarat ini, dengan pemberitahuan bila memungkinkan.</p>

<h2>11. Perubahan Syarat</h2>
<p>Perubahan Syarat akan diumumkan pada halaman ini. Penggunaan berkelanjutan setelah perubahan berarti menerima Syarat yang baru.</p>

<h2>12. Hukum yang berlaku dan penyelesaian sengketa</h2>
<p>Syarat ini tunduk pada hukum Republik Indonesia. Sengketa diselesaikan secara musyawarah, dan apabila gagal, melalui pengadilan [domisili pengadilan].</p>

<h2>13. Kontak</h2>
<p>Pertanyaan mengenai Syarat ini dapat disampaikan melalui [email kontak].</p>
HTML,
            ],
        );

        Page::firstOrCreate(
            ['slug' => 'privasi'],
            [
                'title' => 'Kebijakan privasi',
                'meta_description' => 'Cara kami mengumpulkan, menggunakan, dan melindungi data pribadi kamu.',
                'is_published' => true,
                'content' => <<<'HTML'
<blockquote>
  <p><strong>Draf template.</strong> Teks bertanda [...] harus diisi atau disesuaikan dengan praktik sebenarnya. Minta penasihat hukum meninjau dokumen ini sebelum dipublikasikan, lalu hapus kotak ini.</p>
</blockquote>

<h2>1. Pendahuluan dan pengendali data</h2>
<p>Kebijakan ini menjelaskan bagaimana [nama badan hukum] ("kami") mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi kamu saat memakai NextUs. Kami memproses data pribadi sesuai peraturan perundang-undangan yang berlaku, termasuk Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi. Dalam kebijakan ini, kami bertindak sebagai Pengendali Data Pribadi.</p>

<h2>2. Data yang kami kumpulkan</h2>
<p>Kami hanya mengumpulkan data yang diperlukan untuk menjalankan Layanan: data akun (nama, email, kata sandi terenkripsi, peran, lembaga), data belajar (kursus yang diikuti, progres, hasil kuis), dan data teknis (log akses dan cookie yang diperlukan).</p>

<h2>3. Tujuan dan dasar pemrosesan</h2>
<p>Data dipakai untuk menyediakan layanan belajar, menerbitkan sertifikat, memproses pembayaran/donasi, dan mengirim notifikasi yang kamu setujui.</p>

<h2>4. Data pada halaman verifikasi sertifikat</h2>
<p>Halaman verifikasi menampilkan nama peserta, judul kursus, dan tanggal terbit sesuai persetujuan peserta, agar keaslian sertifikat dapat diperiksa publik.</p>

<h2>5. Pembagian data kepada pihak lain</h2>
<p>Kami tidak menjual data pribadi. Data dapat dibagikan kepada penyedia layanan (mis. Midtrans, penyedia email) sejauh diperlukan untuk menjalankan Layanan.</p>

<h2>6. Cookie dan penyimpanan lokal</h2>
<p>Kami menggunakan cookie yang diperlukan untuk sesi masuk dan preferensi bahasa. Tidak ada cookie pelacak iklan.</p>

<h2>7. Lama penyimpanan</h2>
<p>Data disimpan selama akun aktif dan sesuai kewajiban hukum yang berlaku, lalu dihapus atau dianonimkan.</p>

<h2>8. Keamanan</h2>
<p>Kata sandi disimpan dalam bentuk hash, koneksi dilindungi HTTPS, dan akses data dibatasi berdasarkan peran.</p>

<h2>9. Hak kamu</h2>
<p>Kamu berhak mengakses, memperbarui, dan menghapus data pribadimu, serta menarik persetujuan pemberian data, melalui [email kontak].</p>

<h2>10. Data anak</h2>
<p>Layanan tidak ditujukan untuk anak di bawah [13] tahun. Jika kamu menemukan data anak terkumpul tanpa izin, hubungi kami.</p>

<h2>11. Transfer data ke luar negeri</h2>
<p>Data dapat diproses pada layanan yang berlokasi di luar Indonesia. Kami memastikan perlindungan yang setara dengan undang-undang yang berlaku.</p>

<h2>12. Perubahan kebijakan</h2>
<p>Perubahan kebijakan akan diumumkan pada halaman ini beserta tanggal berlakunya.</p>

<h2>13. Kontak dan pejabat pelindungan data</h2>
<p>Untuk pertanyaan privasi, hubungi [email kontak] atau pejabat pelindungan data kami di [kontak].</p>
HTML,
            ],
        );
    }
}
