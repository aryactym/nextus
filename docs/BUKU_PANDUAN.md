# Buku Panduan NextUs

Platform Belajar Online untuk Filantropi Islam

---

## Daftar Isi

1. [Pendahuluan](#pendahuluan)
2. [Instalasi & Seeder Demo](#instalasi--seeder-demo)
3. [Akun Demo](#akun-demo)
4. [Akses dan Login](#akses-dan-login)
5. [Dashboard Belajar (Peserta)](#dashboard-belajar-peserta)
6. [Katalog Kursus (Publik)](#katalog-kursus-publik)
7. [Halaman Detail Kursus](#halaman-detail-kursus)
8. [Halaman Pelajaran dan Kuis](#halaman-pelajaran-dan-kuis)
9. [Pembayaran dan Midtrans](#pembayaran-dan-midtrans)
10. [Sertifikat dan Verifikasi](#sertifikat-dan-verifikasi)
11. [Blog](#blog)
12. [Panel Admin — Gambaran Umum](#panel-admin--gambaran-umum)
13. [Manajemen Kursus](#manajemen-kursus)
14. [Manajemen Kategori](#manajemen-kategori)
15. [Manajemen Pengguna](#manajemen-pengguna)
16. [Manajemen Pembayaran](#manajemen-pembayaran)
17. [Manajemen Blog](#manajemen-blog)
18. [Manajemen Halaman Statis](#manajemen-halaman-statis)
19. [Pengaturan Sistem](#pengaturan-sistem)
20. [Fitur Manajemen Organisasi (Org Admin)](#fitur-manajemen-organisasi-org-admin)
21. [Laporan Capaian Pembelajaran](#laporan-capaian-pembelajaran)
22. [Import Peserta via CSV](#import-peserta-via-csv)
23. [Manajemen Requirement Kursus](#manajemen-requirement-kursus)
24. [Troubleshooting](#troubleshooting)

---

## Pendahuluan

NextUs adalah platform pembelajaran online untuk filantropi Islam. Platform mendukung pengelolaan katalog kursus, pemantauan progres peserta, penerbitan sertifikat digital, pembayaran via Midtrans, manajemen blog, dan fitur khusus untuk **organisasi mitra** yang ingin mengirim peserta ke pelatihan secara terpusat.

**Fitur utama:**
- Katalog kursus publik dengan filter topik, tingkat, durasi, dan akses
- Pemutar pelajaran (video, teks, PDF) dan kuis
- Enrollment gratis maupun berbayar (Midtrans)
- Sertifikat digital otomatis dengan QR verifikasi
- Panel admin lengkap (kursus, pengguna, pembayaran, blog, pengaturan)
- **Manajemen organisasi mitra** — tambah peserta, import CSV, laporan capaian
- Blog dengan editor WYSIWYG (HugeRTE)
- Share kursus & artikel ke WA, X, LinkedIn, copy link

---

## Instalasi & Seeder Demo

```bash
# Clone dan install dependency
composer install
npm install
cp .env.example .env
php artisan key:generate

# Migrasi dan seed data demo lengkap
php artisan migrate:fresh --seed

# Build aset frontend
npm run build
```

Seeder default mengisi:
1. Organisasi **Yayasan Filantropi Islam** + Super Admin
2. Role dan permission sistem
3. Data pendidikan (Pendidikan seeder)
4. Halaman statis Syarat & Privasi
5. 15 kursus demo lengkap (modul, pelajaran, kuis) — `DemoSeeder`
6. Organisasi **MPZ Gerak Bareng** + 10 peserta + 73 enrollment — `MpzSeeder`

Untuk menjalankan hanya seeder MPZ tanpa reset database:

```bash
php artisan db:seed --class=MpzSeeder
```

---

## Akun Demo

Semua akun menggunakan password: **`password`**

### Platform Operator

| Nama | Email | Password | Role |
|---|---|---|---|
| Super Admin | `admin@example.com` | `password` | Super Admin |

### MPZ Gerak Bareng — Admin Organisasi

| Nama | Email | Password | Role | Jabatan |
|---|---|---|---|---|
| Admin MPZ | `admin@mpzgerakbareng.org` | `password` | Org Admin | Koordinator Pelatihan |

### MPZ Gerak Bareng — Peserta (10 orang)

| Nama | Email | Password | Jabatan | Kategori |
|---|---|---|---|---|
| Ahmad Fauzi | `ahmad.fauzi@mpzgerakbareng.org` | `password` | Staf Program | Officer |
| Siti Rahmawati | `siti.r@mpzgerakbareng.org` | `password` | Staf Keuangan | Officer |
| Budi Santoso | `budi.s@mpzgerakbareng.org` | `password` | Supervisor Program | Supervisor |
| Dewi Kusuma | `dewi.k@mpzgerakbareng.org` | `password` | Relawan Senior | Officer |
| Hendra Wijaya | `hendra.w@mpzgerakbareng.org` | `password` | Manajer Lapangan | Manajer |
| Fatimah Azzahra | `fatimah.a@mpzgerakbareng.org` | `password` | Staf Komunikasi | Officer |
| Rizky Pratama | `rizky.p@mpzgerakbareng.org` | `password` | Koordinator Relawan | Supervisor |
| Nurul Hidayah | `nurul.h@mpzgerakbareng.org` | `password` | Analis Data | Officer |
| Andi Kurniawan | `andi.k@mpzgerakbareng.org` | `password` | Staf IT | Officer |
| Maya Sari | `maya.s@mpzgerakbareng.org` | `password` | Direktur Program | Direktur |

---

## Akses dan Login

### URL Penting

| Halaman | URL |
|---|---|
| Beranda | `/` |
| Login | `/login` |
| Register | `/register` |
| Katalog kursus | `/kursus` |
| Blog | `/blog` |
| Dashboard belajar | `/belajar` |
| Verifikasi sertifikat | `/verifikasi` |
| Panel admin | `/admin` |

### Role Pengguna

| Role | Akses |
|---|---|
| **Super Admin** | Semua menu + pilih laporan semua organisasi |
| **Admin** | Kursus, pengguna, pembayaran, blog, halaman, pengaturan, laporan |
| **Org Admin** | Kelola peserta organisasinya + lihat laporan capaian |
| **Instructor** | Kelola kursus, modul, pelajaran, kuis |
| **Learner** | Belajar, kuis, sertifikat, riwayat transaksi |
| **Guest** | Katalog, preview kursus, verifikasi sertifikat |

---

## Dashboard Belajar (Peserta)

URL: `/belajar`

Tersedia setelah login. Menampilkan:
- Kursus yang sedang diikuti beserta progress bar
- Tombol lanjutkan belajar
- Riwayat sertifikat
- Capaian dan achievement

---

## Katalog Kursus (Publik)

URL: `/kursus`

Dapat diakses siapa saja tanpa login. Fitur:
- Pencarian judul
- Filter multi-pilih: topik, tingkat (pemula/menengah/lanjutan), akses (gratis/donasi/berbayar), durasi
- Urutkan: A–Z, durasi tersingkat, durasi terpanjang
- Facet count — jumlah kursus per filter ditampilkan

---

## Halaman Detail Kursus

URL: `/kursus/{slug}`

Menampilkan:
- Judul, ringkasan, kategori, level, durasi, modul/pelajaran count
- Thumbnail dan tombol daftar
- Tab "Tentang", "Yang akan dipelajari", "Kurikulum" (accordion per modul)
- Bagian **Persyaratan** — dinamis berdasarkan requirement yang diset admin:
  - Kursus prasyarat (dengan link ke halaman kursus tersebut)
  - Nilai minimal kursus prasyarat
  - Pendidikan minimal peserta
  - Batasan usia
  - Catatan tambahan
- **Share widget** di sidebar kartu daftar: tombol WA, X, LinkedIn, salin tautan

---

## Halaman Pelajaran dan Kuis

URL: `/belajar/{slug-kursus}/{uuid-pelajaran}`

### Tipe Pelajaran
| Tipe | Tampilan |
|---|---|
| `video` | Embed video URL |
| `text` | Konten teks/HTML |
| `pdf` | File PDF inline |
| `quiz` | Soal pilihan ganda |

### Navigasi
- Breadcrumb kursus → modul → pelajaran
- Sidebar kurikulum dengan status selesai (✓) per pelajaran
- Tombol "Tandai selesai" untuk tipe non-kuis
- Tombol "Sebelumnya" / "Berikutnya"

### Kuis
- Soal pilihan ganda, jawab semua → submit
- Nilai muncul langsung, dibandingkan `passing_grade` kursus
- Lulus → pelajaran otomatis ditandai selesai
- Kuis pelajaran lulus → sistem langsung arahkan ke post-test modul berikutnya (jika ada)

### Pre-test & Post-test per Modul
Pre-test dan post-test adalah **modul tersendiri** (`type`: `content` / `pre_test` / `post_test`) dengan quiz terkait otomatis dibuat.
- **Pre-test**: wajib diisi sebelum mengakses materi modul; lulus (passing grade) → masuk ke pelajaran pertama modul.
- **Post-test**: wajib lulus untuk melanjutkan ke modul berikutnya / menyelesaikan course.
- Gagal post-test → boleh mengulang sampai lulus; course belum selesai.
- Di panel admin, setiap modul memiliki tombol "Tambah Pre-test" dan "Tambah Post-test".
- Soal dikelola seperti kuis biasa (pertanyaan, pilihan, jawaban benar); bisa diisi manual atau impor Excel (tombol "Unduh Template" & "Impor" di halaman edit kursus).
- Nilai akhir course (`final_score`) = rata-rata skor post-test lulus terakhir per modul.

---

## Pembayaran dan Midtrans

Kursus `donation` atau `paid` membutuhkan pembayaran.

### Alur
1. Peserta klik enroll → sistem buat order → Snap token dari Midtrans
2. Modal Snap terbuka (QRIS, VA, e-wallet, dll.)
3. Setelah sukses, webhook Midtrans mengaktifkan enrollment

### Status Order

| Status | Keterangan |
|---|---|
| `pending` | Menunggu pembayaran |
| `paid` | Lunas, akses dibuka |
| `failed` | Gagal |
| `expired` | Kadaluarsa |

### Uji Sandbox
1. Admin → Pengaturan → isi Server key + Client key Sandbox
2. Pastikan mode production tidak dicentang
3. Klik "Uji koneksi Midtrans"

---

## Sertifikat dan Verifikasi

### Penerbitan Otomatis
Sertifikat terbit ketika enrollment berstatus `completed`: semua pelajaran selesai **dan semua post-test modul lulus**. Kursus tanpa post-test selesai saat semua pelajaran selesai. Tipe sertifikat:
- `free` → terbit langsung setelah selesai
- `donation` / `paid` → terbit setelah pembayaran settlement via webhook

### Halaman Sertifikat
URL: `/sertifikat/{kode}`

Menampilkan nama peserta, judul kursus, tanggal terbit, kode unik, QR verifikasi, dan segel NextUs.

**Aksi:**
- Cetak / simpan PDF (landscape A4)
- Tambahkan ke LinkedIn (data terisi otomatis)

### Verifikasi
URL: `/verifikasi/{kode}`

Masukkan kode untuk memeriksa keaslian. Status: valid, dicabut, atau tidak ditemukan.

---

## Blog

### Publik

| Halaman | URL |
|---|---|
| Daftar artikel | `/blog` |
| Detail artikel | `/blog/{slug}` |

Setiap artikel dilengkapi **share widget** (WA, X, LinkedIn, salin tautan) dan sidebar artikel terkait.

### Admin
URL: `/admin/posts`

- Daftar artikel dengan filter judul dan status (draf/terbit)
- Editor WYSIWYG **HugeRTE** (toolbar lengkap: heading, bold, italic, link, gambar, tabel, code)
- Auto-generate slug dari judul saat membuat artikel baru
- Upload thumbnail (JPG/PNG/WebP, maks 2 MB)
- Status draf/terbit — tanggal terbit otomatis diset saat pertama kali diterbitkan

---

## Panel Admin — Gambaran Umum

URL: `/admin`

### Sidebar Menu

| Seksi | Menu | Permission |
|---|---|---|
| Ringkasan | Dashboard | Semua admin |
| Katalog | Kategori | `manage categories` |
| Katalog | Kursus | `manage courses` |
| Blog | Artikel | `manage blog` |
| Transaksi | Pembayaran | `manage payments` |
| **Organisasi** | **Peserta** | `manage org learners` |
| **Organisasi** | **Laporan Capaian** | `view org reports` |
| Konten | Halaman | `manage pages` |
| Sistem | Pengguna | `manage users` |
| Sistem | Pengaturan | `manage settings` |

---

## Manajemen Kursus

URL: `/admin/courses`

### Daftar Kursus
Filter: pencarian judul/slug, status, tipe akses, kategori. Klik nama kursus → halaman detail.

### Halaman Detail Kursus (Admin)
URL: `/admin/courses/{uuid}`

Menampilkan stat: total peserta, selesai, sertifikat, pendapatan. Tabel peserta dengan progress, tanggal selesai, link pembayaran.

### Tambah/Ubah Kursus
- Field: judul (ID & EN), slug, kategori, level, durasi, ringkasan, deskripsi (**HugeRTE**)
- Sidebar: status, tipe akses, tipe sertifikat, passing grade, thumbnail, latar sertifikat
- Modul & pelajaran dikelola di halaman edit (accordion per modul)
- Setiap modul dapat memiliki **Pre-test** dan **Post-test** (kuis wajib sebelum/sesudah modul)
- Requirement kursus (prasyarat, pendidikan, usia) diset di panel bawah halaman edit

---

## Manajemen Kategori

URL: `/admin/categories`

CRUD kategori dengan nama bilingual (Indonesia + Inggris) dan urutan tampil. Kategori dipakai sebagai topik di katalog dan laporan.

---

## Manajemen Pengguna

URL: `/admin/users`

- Daftar pengguna dengan filter nama/email/jabatan/organisasi dan kategori jabatan
- Tambah/ubah: nama, email, password, role, pendidikan, jabatan, kategori jabatan, nama lembaga
- Hapus (soft delete)

> **Catatan:** Halaman ini untuk Super Admin / Admin platform. Untuk mengelola peserta satu organisasi, gunakan `/admin/org/learners` (Org Admin).

---

## Manajemen Pembayaran

URL: `/admin/payments`

### Filter
- Pencarian order ID / nama / email (searchable)
- **Nama pelatihan** — dropdown Tom Select (searchable)
- **Status** — pending / lunas / gagal / kadaluarsa
- **Periode** — date range picker Litepicker

Tabel menampilkan kolom Tanggal (dibuat + tanggal lunas jika ada).

### Detail Pembayaran
URL: `/admin/payments/{uuid}`

Menampilkan informasi order lengkap dan riwayat webhook Midtrans.

---

## Manajemen Blog

URL: `/admin/posts`

Lihat bagian [Blog](#blog) di atas untuk panduan lengkap editor dan publikasi.

---

## Manajemen Halaman Statis

URL: `/admin/pages`

Edit konten halaman **Syarat & Ketentuan** dan **Kebijakan Privasi** menggunakan editor **HugeRTE** (sama dengan blog dan kursus).

---

## Pengaturan Sistem

URL: `/admin/settings`

### Pelacakan & SEO
- Google Analytics (GA4) — Measurement ID
- Google Search Console — meta tag verification
- Facebook Pixel — Pixel ID

### SMTP Email
Host, port, username, password, enkripsi, email & nama pengirim. Klik **"Kirim email uji"** untuk test via Mailpit (Laragon: `http://127.0.0.1:8025`).

### Midtrans
Server key, client key, toggle production/sandbox. Klik **"Uji koneksi Midtrans"**.

### Favicon
Upload gambar (PNG/JPG/WebP/GIF/AVIF, maks 1 MB). Ditampilkan di tab browser dan layar awal ponsel.

---

## Fitur Manajemen Organisasi (Org Admin)

Fitur ini untuk **admin organisasi mitra** yang mengirim peserta ke pelatihan.

### Hak Akses
Role **Org Admin** otomatis mendapat permission:
- `manage org learners` — kelola peserta, import CSV
- `view org reports` — lihat laporan capaian

Role **Admin** dan **Super Admin** juga punya kedua permission ini.

### Manajemen Peserta
URL: `/admin/org/learners`

| Fitur | Keterangan |
|---|---|
| Daftar peserta | Filter nama/email/jabatan + status aktif/nonaktif |
| Detail peserta | Stat cards + tabel kursus per peserta |
| Tambah manual | Form nama, email, password, jabatan, pendidikan |
| Ubah peserta | Update data tanpa reset enrollment |
| Nonaktifkan | Soft delete — data tetap ada, peserta tidak bisa login |
| Aktifkan kembali | Restore dari soft delete |
| Import CSV | Upload massal (lihat bagian [Import CSV](#import-peserta-via-csv)) |

> Semua operasi otomatis dibatasi ke organisasi admin yang login. Tidak bisa akses peserta organisasi lain.

---

## Laporan Capaian Pembelajaran

URL: `/admin/org/reports`

### Filter
- **Organisasi** *(Super Admin / Admin saja)* — dropdown pilih org, auto-submit saat berubah
- **Periode** — date range picker Litepicker
- **Kursus** — dropdown Tom Select searchable (tersedia di tab Ringkasan & Per Peserta)

### Tab Laporan

#### Ringkasan
Stat cards: total peserta aktif, kursus diikuti, kursus diselesaikan (+ %), sertifikat, rata-rata progres (progress bar). Akses cepat ke tab lain.

#### Per Topik
Tabel: topik, peserta terdaftar, selesai, tingkat penyelesaian (badge hijau/oranye/merah), rata-rata progres.

#### Per Kursus
Tabel: kursus, topik, peserta, selesai, rata-rata progres, sertifikat. Tombol **Detail** → drill-down daftar peserta di kursus tersebut dengan progress individu.

#### Per Peserta
Tabel: peserta, jabatan, kursus diikuti, selesai, sertifikat, rata-rata progres. Tombol **Detail** → halaman profil peserta.

### Ekspor CSV
Tombol **Unduh CSV** tersedia di tab Per Topik, Per Kursus, dan Per Peserta. Filter yang aktif ikut ke dalam ekspor.

### URL Demo (MPZ Gerak Bareng)

| Skenario | URL |
|---|---|
| Laporan ringkasan | `/admin/org/reports?org_id=2` |
| Per topik | `/admin/org/reports/by-topic?org_id=2` |
| Per kursus | `/admin/org/reports/by-course?org_id=2` |
| Per peserta | `/admin/org/reports/by-learner?org_id=2` |

---

## Import Peserta via CSV

URL: `/admin/org/learners-import`

### Format File

| Kolom | Wajib | Keterangan |
|---|---|---|
| `name` | ✅ | Nama lengkap |
| `email` | ✅ | Email unik, dipakai untuk login |
| `password` | ✅ | Min. 8 karakter |
| `nama_jabatan` | Opsional | cth: Staf Program |
| `kategori_jabatan` | Opsional | Officer / Supervisor / Manajer / dll. |

Unduh template di: `/admin/org/learners-import/template`

**Contoh isi file:**
```csv
name,email,password,nama_jabatan,kategori_jabatan
Ahmad Fauzi,ahmad@contoh.org,Pass1234!,Staf Program,Officer
Siti Rahma,siti@contoh.org,Pass1234!,,
```

### Proses & Hasil
- Maks 500 baris per upload, maks 2 MB, encoding UTF-8
- Email yang sudah terdaftar dilewati (tidak error, dilaporkan)
- Baris tidak valid dilaporkan — baris lain tetap diproses
- Semua peserta baru otomatis mendapat role **Learner**
- Halaman hasil menampilkan: berhasil / dilewati / detail error per baris

---

## Manajemen Requirement Kursus

Diatur di halaman **Ubah Kursus** → panel "Syarat kelulusan" (bawah kanan).

### Komponen

| Field | Keterangan |
|---|---|
| Kursus prasyarat | Peserta harus sudah menyelesaikan kursus ini |
| Nilai minimal | Nilai kuis kursus prasyarat minimal sekian % |
| Pendidikan minimal | Tingkat pendidikan peserta (dari data profil) |
| Usia minimal / maksimal | Batasan usia peserta |
| Catatan | Teks bebas yang ditampilkan di halaman kursus publik |

### Tampilan Publik
Semua requirement yang diisi ditampilkan secara dinamis di seksi **"Persyaratan"** halaman detail kursus publik, termasuk link ke halaman kursus prasyarat.

### Perilaku Enroll
Jika requirement diset, sistem memvalidasi sebelum enrollment diproses. Peserta yang tidak memenuhi syarat ditolak dengan pesan error yang jelas.

---

## Troubleshooting

| Masalah | Kemungkinan Penyebab | Solusi |
|---|---|---|
| Halaman 404 akses kursus | Kursus belum published atau slug salah | Cek status dan slug di admin |
| Enroll gagal (requirement) | Prasyarat pendidikan/kursus belum terpenuhi | Cek profil peserta dan riwayat enroll |
| Midtrans modal tidak terbuka | Key salah atau CSP blokir | Cek pengaturan Midtrans, periksa console browser |
| Email tidak terkirim | Konfigurasi SMTP belum benar | Uji email di Pengaturan, cek Mailpit |
| Sertifikat tidak muncul | Enrollment belum completed atau webhook belum masuk | Cek status enrollment dan log webhook |
| HugeRTE tidak muncul | Script CDN diblokir atau gagal load | Periksa console browser, pastikan CDN dapat diakses |
| Laporan org kosong | Belum ada enrollment dari peserta org tersebut | Pastikan peserta sudah enroll dan `organization_id` benar |
| Import CSV gagal | Format header tidak sesuai atau encoding bukan UTF-8 | Gunakan template CSV yang disediakan |
| Redirect ke `/admin/courses/9` saat simpan | Bug lama — sudah diperbaiki | Pastikan menggunakan versi terbaru |

### Log dan Debug
- Log aplikasi: `storage/logs/laravel.log`
- Log browser: DevTools → Console
- Mailpit: `http://127.0.0.1:8025`

---

## Stack Teknis

| Komponen | Library |
|---|---|
| Framework | Laravel 13.x (PHP 8.4) |
| Admin UI | Tabler Core 1.7.3 |
| Frontend | Alpine.js 3.x, Vite, Tailwind CSS 4 |
| Rich text editor | HugeRTE (via Tabler CDN) |
| Date range picker | Litepicker (via Tabler CDN) |
| Searchable select | Tom Select (via Tabler CDN) |
| i18n | mcamara/laravel-localization |
| Pembayaran | Midtrans Snap |
| PDF sertifikat | Barryvdh DomPDF |
| QR code | SimpleSoftwareIO Simple QRCode |
| Hak akses | Spatie Laravel Permission |
| Translatable models | Spatie Laravel Translatable |
| Image upload | Intervention Image 4.x |
| Arsitektur | Domain-driven (`app/Domain/`) |

---

*Dokumen ini diperbarui secara berkala mengikuti perkembangan fitur.*
