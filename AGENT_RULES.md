# Aturan Agent AI (opencode / freebuff / kiro / antigravity)

Baca file ini SEBELUM mulai bekerja. Aturan ini berlaku untuk semua agent AI yang bekerja di repo ini.

## 1. Baca dulu, baru kerja

- Baca `AGENTS.md`, `AGENT_RULES.md`, `WORK_PLAN.md`, dan `docs/System Design Platform Pelatihan & Sertifikasi Online Filantropi Islam.md` sebelum menambah/mengubah kode.
- Cek `WORK_PLAN.md` untuk fase aktif, progress, masalah saat ini, dan next move. Update file itu setelah sesi implementasi yang mengubah progress atau next move; jangan ubah status untuk pekerjaan dokumentasi saja.
- Jangan menebak fitur. Gunakan dokumen desain dan work plan untuk ruang lingkup/target; periksa kode untuk memastikan perilaku dan implementasi yang sudah ada.

## 2. Versi PHP (Windows 11 + Laragon)

Composer dependencies butuh PHP >= 8.4.1. Jangan gunakan PHP 8.3.30 bawaan CLI.
Sebelum menjalankan artisan/composer, jalankan dulu:

```powershell
$env:PATH = "D:\laragon\bin\php\php-8.4.23-nts-Win32-vs17-x64;" + $env:PATH
php artisan ...
composer ...
```

## 3. Komponen reusable

Gunakan kembali komponen yang sudah ada. Jika pola UI dipakai di beberapa halaman, buat komponen bersama daripada menduplikasi kode. Daftar berikut adalah pola/target desain, bukan jaminan semua komponen sudah tersedia; periksa `resources/views/components` dan kode terkait sebelum menggunakannya.

| Area | Komponen reusable |
| --- | --- |
| Blade layout | `x-layout` (wrapper Tabler + sidebar + navbar + yield content) |
| SEO | `x-seo` (title, description, canonical, robots, hreflang, OG, JSON-LD) |
| UI | `x-card`, `x-button`, `x-form-input`, `x-form-select`, `x-form-checkbox`, `x-flash`, `x-pagination` |
| Upload | `x-upload-dropzone` + `App\Services\UploadService` (aman, Windows + Linux) |
| Media | `x-media-player` (video allowlist YouTube/Vimeo/Bunny, teks, PDF) |
| Progres | `x-progress-bar` (kursus & pelajaran) |
| Breadcrumb | `x-breadcrumb` |
| Tanggal/format | Helper atau ViewComposer untuk ID/EN |

Traits/pola reusable yang direncanakan: `HasUuid`, `BelongsToOrganization`, `Translatable` (JSON fallback), dan `SoftDeletes` bila relevan. Periksa implementasi aktual sebelum mengasumsikan trait atau helper tersedia.

## 4. Skema upload aman (Windows 11 + Linux Ubuntu)

Simpan di luar webroot (`storage/app/private` atau `storage/app/uploads`). Sajikan via route/controller Laravel dengan otorisasi, bukan langsung via URL publik.

Aturan wajib:
- Allowlist ekstensi dan MIME type (jangan cuma ekstensi).
- Nama file acak (UUID) + folder per tanggal/organisasi, hindari collision.
- Simpan di luar `public/` agar tidak bisa dieksekusi langsung oleh server web.
- Saat selesai dieksekusi, gunakan `Storage::disk()` (Laravel Filesystem) agar path abstract dan jalan di Windows (backslash) dan Linux (forward slash).
- Header respons: `Content-Disposition: attachment`, `X-Content-Type-Options: nosniff`, `Cache-Control` sesuai jenis.
- Signed URL/temporary URL untuk file privat (sertifikat, materi terbatas).
- Re-encode gambar (Intervention Image) untuk hilangkan metadata / polyglot.
- Validasi ukuran maksimal baik di sisi client dan server.
- Tidak pernah percayai `$_FILES['name']` atau path yang dikirim client.

Pola implementasi yang terbukti jalan di Windows 11 + PHP 8.4 (Laravel 13):

```php
// UploadService::upload()
$pathname = $file->getPathname(); // JANGAN getRealPath(): sering false di Windows temp

if (! $file->isValid()) { /* throw ValidationException */ }
if (! $pathname || ! is_file($pathname) || filesize($pathname) === 0) { /* throw */ }

// Re-encode: decode dari isi file, bukan path (hindari cek is_readable pada direktori)
$image = (new ImageManager(new Driver))->decode(file_get_contents($pathname));

// Simpan ke storage/app/temp (BUKAN sys_get_temp_dir(): sering tidak readable/writable)
$tempDir = storage_path('app/temp');
if (! is_dir($tempDir)) { mkdir($tempDir, 0775, true); }
$outputPath = $tempDir.DIRECTORY_SEPARATOR.(string) Str::uuid().'.'.$extension;
$image->save($outputPath);

// ... Storage::disk('uploads')->put($path, file_get_contents($outputPath))
// bersihkan file temp setelah selesai
```

- `UploadController` (Laravel 13): jangan pakai `$this->middleware()` di constructor. Pasang middleware di route:
  ```php
  Route::get('uploads/{path}', [UploadController::class, 'show'])
      ->middleware(['auth', 'signed'])
      ->where('path', '.*');
  ```
- Import `Illuminate\Support\Facades\Storage` di controller (jangan andalkan facade tanpa use).
- URL file privat di view wajib **signed**, bukan `route()` biasa:
  ```blade
  <img src="{{ \Illuminate\Support\Facades\URL::signedRoute('admin.uploads.show', ['path' => $course->thumbnail]) }}">
  ```
- File PDF/materi (kategori `document`) tidak perlu re-encode, tapi tetap validasi MIME + ukuran.
- Pastikan `storage/app/temp` dan `storage/app/private/uploads` dapat ditulis proses web server.

## 5. Keamanan (OWASP Top 10 + ASVS L2)

- CSRF token di semua form (webhook Midtrans dikecualikan, dilindungi signature).
- Eloquent binding, tanpa string concat query.
- Blade `{{ }}` escaping, hindari `{!! !!}`.
- HTMLPurifier untuk sanitasi HTML materi.
- Rate limit login, upload, webhook.
- Password Argon2id/bcrypt, cookie Secure/HttpOnly/SameSite.
- UUID sebagai primary key publik; cek kepemilikan resource (anti-IDOR).
- Global scope `organization_id` + Policy untuk multi-tenancy.
- Tidak simpan sensitif di log.

## 6. Bahasa & i18n

- Target: URL prefix locale (`/id`, `/en`). Periksa rute dan middleware aktual; jangan menganggap semua halaman sudah memakai prefix locale.
- UI string di `lang/id`, `lang/en`.
- Konten kursus kolom JSON translatable dengan fallback.
- Sertifikat dan email bilingual; learner pilih bahasa sertifikat.

## 7. Database

- Untuk tabel entitas bisnis baru: `id` BIGINT internal + `uuid` publik, timestamps, soft delete bila relevan. Tabel pivot, tabel framework, dan tabel log dapat mengikuti kebutuhan masing-masing.
- Kolom teks diterjemahkan disimpan JSON `{id: ..., en: ...}`.
- Migrasi di `database/migrations`, seeder di `database/seeders`.
- Jangan ubah migrasi yang sudah di-produksi tanpa buat migrasi baru.

## 8. Commit

- Setiap fase besar = satu commit di lokal.
- Pesan commit: `feat: [fase] deskripsi singkat` atau `fix: ...`.
- Jangan commit file `.env`, `vendor/`, `node_modules/`, `storage/`, `bootstrap/cache/`.

## 9. Akses lokal

- Aplikasi di lokal: `nextus.test` atau folder `D:\laragon\www\nextus\` atau `localhost/nextus`.
- Pastikan bisa diakses sebelum lanjut fase berikutnya.

## 10. Testing & kualitas

- Pest + Larastan.
- Tes lintas-tenant: user organisasi A tidak bisa baca/ubah data organisasi B.
- Tes idempotensi webhook Midtrans.
- Tes upload di Windows dan simulasi path Linux.

## 11. Template halaman website utama

Referensi template HTML website utama ada di folder `docs/`:

| File | Halaman |
| --- | --- |
| `docs/home-akademi-filantropi.html` | Beranda |
| `docs/katalog-akademi-filantropi.html` | Katalog kursus |
| `docs/login-akademi-filantropi.html` | Masuk |
| `docs/registrasi-akademi-filantropi.html` | Registrasi |
| `docs/tentang-akademi-filantropi.html` | Tentang kami |
| `docs/verifikasi-sertifikat-akademi-filantropi.html` | Verifikasi sertifikat |

Gunakan file-file tersebut sebagai referensi desain saat mengerjakan halaman terkait. Implementasi dan rute aktif tetap harus diperiksa di kode aplikasi.

Implementasi saat ini:
- File Blade: `resources/views/home.blade.php` (halaman depan bahasa Indonesia).
- CSS khusus halaman depan diekstrak ke `resources/views/home-style.css`, dikompile lewat Vite bersama `resources/css/app.css`.
- File `resources/views/welcome.blade.php` sengaja tidak dihapus; hanya digunakan untuk boilerplate Laravel, bukan untuk halaman depan publik.
- Rute `/id` mengembalikan `view('home')`; `/` memilih `home-en` bila bahasa pilihan browser adalah Inggris, selain itu `home`. Rute `/en` juga mengarah ke `home-en`, tetapi `resources/views/home-en.blade.php` belum tersedia saat aturan ini ditinjau. Verifikasi view dan rute sebelum menyatakan halaman bahasa Inggris sudah berfungsi.
- Rute masuk dan registrasi aktif di `/login` dan `/register`; template HTML terkait di `docs/` adalah referensi desain, bukan implementasi Blade.
