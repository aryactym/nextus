# Aturan Agent AI (opencode / freebuff / kiro / antigravity)

Baca file ini SEBELUM mulai bekerja. Aturan ini berlaku untuk semua agent AI yang bekerja di repo ini.

## 1. Baca dulu, baru kerja

- Baca `AGENTS.md`, `AGENT_RULES.md`, `WORK_PLAN.md`, dan `docs/System Design Platform Pelatihan & Sertifikasi Online Filantropi Islam.md` sebelum menambah/mengubah kode.
- Cek `WORK_PLAN.md` untuk fase aktif, progress, masalah saat ini, dan next move. Update file itu setelah setiap sesi kerja.
- Jangan menebak fitur. Lihat dokumen desain dan work plan sebagai referensi tunggal (single source of truth).

## 2. Versi PHP (Windows 11 + Laragon)

Composer dependencies butuh PHP >= 8.4.1. Jangan gunakan PHP 8.3.30 bawaan CLI.
Sebelum menjalankan artisan/composer, jalankan dulu:

```powershell
$env:PATH = "D:\laragon\bin\php\php-8.4.23-nts-Win32-vs17-x64;" + $env:PATH
php artisan ...
composer ...
```

## 3. Reusable komponen (wajib)

Buat sekali, pakai berkali-kali. Jangan duplikasi kode.

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

Traits reusable: `HasUuid`, `BelongsToOrganization`, `Translatable` (JSON fallback), `SoftDeletes`.

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

- URL prefix locale (`/id`, `/en`).
- UI string di `lang/id`, `lang/en`.
- Konten kursus kolom JSON translatable dengan fallback.
- Sertifikat dan email bilingual; learner pilih bahasa sertifikat.

## 7. Database

- Semua tabel: `id` BIGINT internal + `uuid` publik, timestamps, soft delete bila relevan.
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
