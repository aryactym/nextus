# Buku Panduan NextUs

Platform Belajar Online untuk Filantropi Islam

---

## Daftar Isi

1. [Pendahuluan](#pendahuluan)
2. [Akses dan Login](#akses-dan-login)
3. [Dashboard Belajar (Peserta)](#dashboard-belajar-peserta)
4. [Katalog Kursus (Publik)](#katalog-kursus-publik)
5. [Halaman Pelajaran dan Kuis](#halaman-pelajaran-dan-kuis)
6. [Pembayaran dan Midtrans](#pembayaran-dan-midtrans)
7. [Sertifikat dan Verifikasi](#sertifikat-dan-verifikasi)
8. [Panel Admin (Manajemen)](#panel-admin-manajemen)
9. [Manajemen Pengaturan (Settings)](#manajemen-pengaturan-settings)
10. [Manajemen Requirement Kursus](#manajemen-requirement-kursus)
11. [Favicon dan Branding](#favicon-dan-branding)
12. [Troubleshooting](#troubleshooting)

---

## Pendahuluan

NextUs adalah platform pembelajaran online yang dirancang untuk filantropi Islam. Platform ini memungkinkan organisasi untuk mengelola katalog kursus, memantau progres belajar peserta, menerbitkan sertifikat digital, dan mengelola pembayaran melalui Midtrans.

Fitur utama:
- Katalog kursus publik dengan filter topik, tingkat, durasi, dan akses
- Pemutar pelajaran (video, teks, PDF) dan kuis
- Enrollment gratis maupun berbayar (Midtrans Sandbox/Produksi)
- Sertifikat digital otomatis dengan QR verifikasi
- Panel admin berbasis Tabler (fluid vertical layout)
- Manajemen pengguna, kategori, halaman, dan pengaturan sistem

---

## Akses dan Login

### URL Akses
- Beranda: `http://nextus.test/`
- Login: `http://nextus.test/login`
- Register: `http://nextus.test/register`
- Katalog: `http://nextus.test/kursus`
- Dashboard belajar (login diperlukan): `http://nextus.test/belajar`

### Role Pengguna
| Role | Akses |
|------|-------|
| Super Admin | Akses penuh semua menu dan pengaturan sistem |
| Admin | Kelola pengguna, kategori, kursus, halaman, pengaturan, transaksi |
| Instructor | Kelola kursus, modul, materi, kuis; lihat progres peserta |
| Learner | Daftar kursus, belajar, kuis, unduh sertifikat, riwayat transaksi |
| Guest | Lihat katalog, preview kursus, ganti bahasa, verifikasi sertifikat |

### Login
1. Buka `/login`
2. Masukkan email dan password
3. Klik "Masuk"
4. Jika belum punya akun, klik "Daftar gratis" di halaman login

---

## Dashboard Belajar (Peserta)

Halaman ini hanya untuk pengguna yang sudah login (role Learner atau yang sama).

### Fitur
- Daftar kursus yang diikuti (enrolled)
- Progress persentase per kursus
- Akses cepat ke pelajaran yang sedang berlangsung
- Tombol untuk melanjutkan pelajaran terakhir

### Cara Mengakses
Setelah login, klik tombol **"Belajar saya"** di header kanan atas, atau buka `http://nextus.test/belajar`.

---

## Katalog Kursus (Publik)

Halaman publik yang dapat diakses oleh siapa saja (termasuk guest).

### Fitur
- Daftar semua kursus dengan status "published"
- Filter: pencarian judul, topik (kategori), tingkat (pemula/menengah/lanjutan), akses (gratis/donasi/berbayar), durasi
- Urutkan: Nama A-Z, durasi tersingkat, durasi terpanjang
- Setiap kursus menampilkan thumbnail, kategori, tingkat, durasi, dan tipe sertifikat

### Cara Mengakses
Buka `http://nextus.test/kursus`.

---

## Halaman Pelajaran dan Kuis

Setelah enroll dalam sebuah kursus, peserta dapat mengakses pelajaran melalui halaman pelanggan.

### Struktur Navigasi
```
Belajar saya → Kursus → Pelajaran
```

### Tampilan Halaman Pelajaran
- **Hero**: judul pelajaran, breadcrumb, durasi, persentase selesai
- **Konten**: video, teks, atau PDF (tergantung tipe pelajaran)
- **Kurikulum (sidebar kanan)**: daftar modul dan pelajaran, pelajaran aktif ditandai
- **Tombol navigasi**: "Sebelumnya", "Tandai selesai", "Berikutnya"

### Kuis
- Setiap pelajaran berjenis "quiz" akan menampilkan soal kuis
- Peserta wajib menjawab semua pertanyaan
- Nilai lulus ditentukan oleh pengaturan kursus (`passing_grade`)
- Setelah lulus, pelajaran ditandai selesai dan peserta dapat melanjutkan ke materi berikutnya

### Tandai Selesai
Untuk pelajaran non-kuis, klik tombol **"Tandai selesai"** untuk mencatat progres.

---

## Pembayaran dan Midtrans

Kursus dengan tipe akses `donation` atau `paid` membutuhkan pembayaran sebelum enroll.

### Alur Pembayaran
1. Peserta klik enroll pada kursus berbayar
2. Sistem membuat order dan meminta Snap token dari Midtrans
3. Snap modal Midtrans terbuka (QRIS, VA, e-wallet, dll.)
4. Setelah pembayaran sukses, webhook Midtrans akan:
   - Mengubah status order menjadi `paid`
   - Membuat enrollment untuk peserta
   - Mengirim email receipt
   - Menerbitkan sertifikat (jika `certificate_type = free`)

### Status Order
| Status | Keterangan |
|--------|------------|
| pending | Menunggu pembayaran |
| paid | Lunas (akses kursus diaktifkan) |
| failed | Pembayaran gagal |
| expired | Order kadaluwarsa |

### Uji Coba Midtrans (Sandbox)
1. Masuk ke admin → Pengaturan
2. Isi **Server key** dan **Client key** Midtrans Sandbox
3. Pastikan `midtrans_is_production` tidak dicentang (mode sandbox)
4. Klik tombol **"Uji koneksi Midtrans"** di panel kanan
5. Coba enroll ke kursus berbayar dan selesaikan pembayaran di modal Snap

> Catatan: Error `net::ERR_BLOCKED_BY_CLIENT` dan `Failed to fetch` ke domain `katulampa.gopay.sh` di console browser adalah telemetri Midtrans yang diblokir ad-blocker, bukan error aplikasi. Pembayaran tetap berjalan normal.

---

## Sertifikat dan Verifikasi

### Penerbitan Sertifikat
Sertifikat diterbitkan otomatis ketika:
- Peserta menyelesaikan semua pelajaran dalam kursus (`enrollment.status = completed`)
- Tipe sertifikat kursus adalah `free` → diterbitkan segera
- Tipe sertifikat `donation` atau `paid` → diterbitkan setelah pembayaran lunas (webhook settlement)

### Halaman Sertifikat
URL: `http://nextus.test/sertifikat/{kode}`

Tampilan sertifikat mencakup:
- Logo NextUs
- Nama peserta
- Judul kursus
- Tanggal terbit
- Kode sertifikat unik (format: `NX-TAHUN-XXXXXXXX`)
- Segel verifikasi NextUs
- QR code untuk verifikasi

### Cetak / Unduh PDF
- Klik **"Cetak / simpan PDF"** untuk mencetak langsung dari browser
- Klik **"Unduh PDF"** untuk mengunduh file PDF (format landscape A4)

### Verifikasi Sertifikat
URL: `http://nextus.test/verifikasi` atau `http://nextus.test/verifikasi/{kode}`

Masukan kode sertifikat untuk memeriksa keaslian sertifikat. Hasil verifikasi menampilkan status: valid, revoked, atau tidak ditemukan.

### Berbagi ke LinkedIn
Klik tombol **"Tambahkan ke LinkedIn"** di halaman sertifikat. Sistem akan membuka halaman LinkedIn dengan data sertifikat terisi otomatis (nama kursus, organisasi, tanggal, kode, dan URL verifikasi).

> Sertifikat versi terbaru dilengkapi field **media** (portofolio) dan **skills** (keterampilan) yang dapat diisi oleh admin. Kedua field ini juga ikut disertakan saat sharing ke LinkedIn.

---

## Panel Admin (Manajemen)

Panel admin menggunakan layout Tabler fluid vertical dengan sidebar NextUs-branded (gradient zamrud ke malam).

URL: `http://nextus.test/admin/`

### Menu Sidebar
| Menu | Role yang Bisa Akses | Keterangan |
|------|---------------------|------------|
| Dashboard | Semua admin | Ringkasan sistem |
| Kategori | `manage categories` | Kelola kategori kursus |
| Kursus | `manage courses` | Kelola kursus, modul, pelajaran, kuis |
| Pembayaran | `manage payments` | Daftar order dan riwayat webhook |
| Halaman | `manage pages` | Edit Syarat & Privasi (CKEditor) |
| Pengguna | `manage users` | Kelola pengguna dan role |
| Pengaturan | `manage settings` | Pengaturan sistem, SMTP, Midtrans, favicon |

> Role **Instructor** hanya memiliki izin `manage courses`, tidak dapat mengakses menu Pengguna atau Pengaturan.

---

## Manajemen Pengaturan (Settings)

URL: `http://nextus.test/admin/settings`

### Pelacakan & Verifikasi
- **Google Analytics (GA4)**: masukkan Measurement ID (contoh: `G-XXXXXXXXXX`)
- **Google Search Console**: masukkan nilai `content` dari meta tag verifikasi
- **Facebook Pixel**: masukkan ID Pixel (angka)

### SMTP Email
Konfigurasi SMTP untuk seluruh email aplikasi (verifikasi, reset password, notifikasi enroll, receipt pembayaran, sertifikat).
- Host, Port, Username, Password/API key (disimpan terenkripsi)
- Enkripsi: TLS (587) / SSL (465) / Tanpa
- Email pengirim dan Nama pengirim

### Pembayaran Midtrans
- **Server key** (rahasia, disimpan terenkripsi)
- **Client key** (biasa)
- Mode production (centang jika bukan sandbox)

### Uji Sistem
- **Kirim email uji**: verifikasi konfigurasi SMTP melalui Mailpit (Laragon: `http://127.0.0.1:8025`)
- **Uji koneksi Midtrans**: buat Snap token uji dan buka modal pembayaran

### Favicon Situs
Unggah gambar favicon (PNG, JPG, WebP, GIF, AVIF, maks 1 MB) untuk ditampilkan di tab browser dan layar awal ponsel.

---

## Manajemen Requirement Kursus

Fitur ini memungkinkan admin menetapkan prasyarat sebelum seorang peserta dapat enroll ke suatu kursus.

### Komponen Requirement
1. **Pendidikan minimal**: peserta harus memiliki pendidikan yang tertulis di profilnya
2. **Kursus prasyarat**: peserta harus sudah menyelesaikan kursus lain sebelumnya
3. **Nilai minimal**: nilai ujian kuis pada kursus prasyarat harus mencapai angka tertentu

### Cara Mengatur Requirement
Requirement saat ini diatur secara langsung melalui database (tabel `course_requirements`) atau via tinker:

```php
// Contoh: buat requirement untuk kursus ID 2
// - Harus sudah lulus kursus ID 1 dengan nilai minimal 75
// - Harus memiliki pendidikan minimal Sarjana (S1)

use App\Domain\Catalog\Models\CourseRequirement;

CourseRequirement::create([
    'course_id' => 2,
    'prerequisite_course_id' => 1,
    'min_grade' => 75,
    'pendidikan_id' => 6, // ID pendidikan Sarjana
    'notes' => 'Wajib lulus Kursus Dasar dengan nilai 75 dan pendidikan S1',
]);
```

### Perilaku Saat Enroll
- Jika requirement tidak diisi (kosong) → enroll langsung diperbolehkan
- Jika ada requirement → sistem cek satu per satu:
  1. Pendidikan peserta >= tingkat yang ditentukan
  2. Kursus prasyarat sudah selesai (status `completed`)
  3. Nilai kuis terbaik >= `min_grade` (jika diisi)
- Jika salah satu gagal → peserta diarahkan kembali ke halaman kursus dengan pesan error

---

## Favicon dan Branding

Favicon dapat diunggah melalui halaman Pengaturan admin (bagian bawah kanan).

### Format yang Didukung
PNG, JPG, WebP, GIF, AVIF (maksimal 1 MB)

### Lokasi Penyimpanan
File favicon disimpan di `storage/public/favicons/` dan diakses langsung via URL publik (tidak butuh signed URL).

### Tampilan
- `<link rel="icon">` di halaman publik
- `<link rel="apple-touch-icon">` untuk layar awal ponsel

---

## Troubleshooting

### Masalah Umum

| Masalah | Penyebab Kemungkinan | Solusi |
|---------|---------------------|--------|
| Halaman 404 saat akses course | Course belum published atau slug salah | Periksa status course dan slug di admin |
| Enroll gagal (error requirement) | Pendidikan prasyarat atau course prasyarat belum terpenuhi | Periksa profil peserta dan riwayat enroll |
| Midtrans modal tidak terbuka | Server key/client key salah atau CSP blokir | Periksa pengaturan Midtrans di admin |
| Email tidak terkirim | Konfigurasi SMTP belum benar | Uji email via halaman Pengaturan, cek Mailpit |
| Sertifikat tidak muncul | Enrollment belum completed atau certificate_type belum sesuai | Periksa status enrollment dan webhook Midtrans |
| Logo sertifikat tidak muncul | File `logo-nextus.png` tidak ada di `public/images/` | Pastikan file logo tersedia |

### Log dan Debug
- Log aplikasi: `storage/logs/laravel.log`
- Log browser: buka DevTools → Console
- Mailpit (Laragon): `http://127.0.0.1:8025`

---

## Kontribusi dan Pengembangan

Sistem ini menggunakan:
- Laravel 13.40.x (PHP 8.4)
- Tabler Core 1.7.3 (admin layout)
- mcamara/laravel-localization (i18n)
- Midtrans Snap (pembayaran)
- Barryvdh DomPDF (cetak PDF sertifikat)
- SimpleSoftwareIO Simple QRCode (QR verifikasi)
- Spatie Laravel Permission (RBAC)
- Intervention Image 4.x (upload gambar)

Semua fitur dikembangkan secara modular di bawah `app/Domain/`.

---

© {{ date('Y') }} NextUs. Seluruh hak dilindungi.
