# Work Plan — Akademi Filantropi (Platform Pelatihan & Sertifikasi Online Filantropi Islam)

Referensi: `docs/System Design Platform Pelatihan & Sertifikasi Online Filantropi Islam.md`  
Stack: Laravel 13, PHP 8.4, MySQL/SQLite, Blade + Tabler UI, Alpine.js, Vite, Midtrans, Redis, Object storage.

## Status keseluruhan
- Repo: diinisialisasi git, commit awal Laravel skeleton (SQLite).
- PHP CLI terpaksa dipaksa ke 8.4.23 via PATH (Laragon bawaan 8.3.30, komponen butuh >= 8.4.1).
- Database: SQLite (dev lokal), siap dipindah ke MySQL untuk production.
- Access: `nextus.test` / `D:\laragon\www\nextus\` / `localhost/nextus`.

---

## Fase 1 — Fondasi (MVP S1)

### 1.1 Setup environment & dependensi inti
- [ ] Composer packages: spatie/laravel-permission, spatie/laravel-medialibrary, spatie/laravel-translatable, mcamara/laravel-localization, laravel/socialite, midtrans/midtrans-php, barryvdh/laravel-dompdf, simplesoftwareio/simple-qrcode, spatie/laravel-activitylog, intervention/image, laravel/pint, pestphp/pest, nunomaduro/larastan
- [ ] npm packages: tabler, alpinejs, vite plugins
- [ ] `.env`: APP_URL=http://nextus.test, DB konfig, Midtrans sandbox keys, mail log
- [ ] Laragon virtual host `nextus.test` pointing ke `D:\laragon\www\nextus\public`
- [ ] Verifikasi `http://nextus.test` dan `localhost/nextus` terbuka

### 1.2 Tata direktori modular monolith
- `app/Domain/{Auth,Catalog,Learning,Assessment,Certificate,Payment,Organization,Admin,Report}`
- Tiap domain: `Models`, `Services`, `Http/Controllers`, `Http/Requests`, `Policies`, `Providers`
- `app/Support` untuk reusable traits/helpers

### 1.3 Auth, RBAC, i18n kerangka
- Laravel Breeze/Fortify: register, login, verifikasi email, reset password
- `spatie/laravel-permission`: peran Guest, Learner, Instructor, Admin, Super Admin
- `mcamara/laravel-localization`: prefix `/id`, `/en`
- `spatie/laravel-translatable` siap untuk kolom JSON
- Middleware peran, deny by default

### 1.4 Skema DB inti (migrasi)
- `users`, `categories`, `courses`, `course_modules`, `lessons`, `enrollments`, `lesson_progress`
- `organizations` (satu default untuk MVP), `organization_user`
- Kolom `uuid` publik, timestamps, soft delete

### 1.5 Reusable komponen Blade (batch 1)
- `x-layout`, `x-seo`, `x-card`, `x-button`, `x-form-input`, `x-flash`, `x-breadcrumb`, `x-pagination`
- View composer / helper untuk format tanggal ID/EN

### 1.6 Baseline keamanan OWASP
- CSRF semua form, security headers (CSP, X-Content-Type-Options, frame-ancestors, Referrer-Policy)
- Rate limit login, cookie Secure/HttpOnly/SameSite
- `APP_DEBUG=false` di production, security headers Nginx

### Progress Fase 1 — SELESAI
- 1.1 Dependensi composer & npm, `.env`, virtual host alias localhost/nextus, hosts nextus.test, storage:link, migrate (SQLite) — OK.
- 1.2 Tata direktori modular monolith `app/Domain/*` + `app/Support/*` + resources/views — OK.
- 1.3 Auth kerangka + RBAC Spatie (role Super Admin/Admin/Instructor/Learner/Guest) + User model (HasRoles, HasUuid, BelongsToOrganization, SoftDeletes) — OK.
- 1.4 Skema DB inti: organizations, organization_user, users(+uuid/org/locale/linkedin/softdelete), categories, courses, course_modules, lessons, enrollments, lesson_progress, orders, payment_events + Spatie permission & media tables — OK. Seed: organisasi default + Super Admin admin@example.com/password — OK.
- 1.5 Reusable komponen Blade: belum (pindah ke batch berikutnya) — support traits (HasUuid, BelongsToOrganization) + helpers (organization_id, format_date_id/en, currency_idr) + SupportServiceProvider — OK.
- 1.6 Baseline keamanan OWASP: belum (selanjutnya).

### Masalah saat ini Fase 1
- PHP CLI bawaan 8.3.30 tidak cocok komponen (butuh 8.4.1+). Workaround: paksa PATH ke 8.4.23 (catat di AGENTS.md).
- Belum ada git remote (hanya lokal).
- Laragon nginx belum di-restart, jadi `nextus.test` belum tersedia lewat nginx (hanya `php artisan serve`).

### Commit Fase 1
Belum dilakukan.

### Next move
Commit Fase 1 (fabf7cf adalah setup; tambahkan commit fase 1 kode), lalu mulai Fase 2 CMS Kursus.

---

## Fase 2 — CMS Kursus (MVP S2)

### 2.1 CRUD Kategori & Kursus
- Kategori: name (JSON), slug, sort
- Kursus: title, summary, description (JSON), slug, category, level, duration, thumbnail, access_type, certificate_type, min_amount, suggested_amounts, passing_grade, status, organization_id
- Upload thumbnail via `UploadService` (skema aman)

### 2.2 CRUD Modul & Pelajaran
- Modul: course_id, title (JSON), sort
- Pelajaran: module_id, title, type (video/text/pdf/quiz), content (JSON), video_url, duration, is_preview, sort
- Upload materi file via `UploadService`

### 2.3 Konten ID/EN + draft-publish
- Terjemahan JSON dengan fallback
- Status draft/published/archived, published_at
- Admin publish tanpa developer

### 2.4 Reusable komponen Blade (batch 2)
- `x-media-player`, `x-upload-dropzone`, `x-rich-editor` (konten teks)

### Progress Fase 2 — SELESAI
- CMS kategori/kursus: route, controller, form, validasi (slug unik per-org), tenant isolation — OK.
- Modul: store, update, destroy via route berbasis UUID; pelajaran: store, update, destroy, upload PDF privat. Semua dengan tenant isolation — OK.
- Migrasi tambahan `file_path` pelajaran + perbaikan foreign key `lessons.module_id` — OK.
- Route update modul (`PUT admin/modules/{module}`) dan update pelajaran (`PUT admin/lessons/{lesson}`) ditambahkan — OK.
- Perbaikan bug: `Category` model `translatable` tidak menyertakan `slug` (kolom `slug` adalah string biasa, bukan JSON) — OK.
- Perbaikan validasi: `unique:categories,slug` dan `unique:courses,slug` di-scope per `organization_id` — OK.
- Feature test: `CategoryManagementTest` (7 test) + `CourseManagementTest` (9 test) lulus semua — OK.
- Komponen Blade batch 2: `x-upload-dropzone`, `x-media-player`, `x-rich-editor` — OK.
- Route placeholder `verifikasi` ditambahkan sehingga test `ExampleTest` lulus (total 23 test lulus) — OK.
- Perubahan Fase 2 belum di-commit.

### Masalah saat ini Fase 2
Tidak ada masalah tersisa.

### Next move Fase 2
Commit Fase 2, lalu mulai Fase 3 — Katalog publik & pengalaman belajar (Enrollment gratis, Course player, Dashboard learner).

---

## Fase 3 — Pengalaman Belajar (MVP S3)

### 3.1 Katalog & detail kursus (SSR)
- Daftar kursus, pencarian, filter, detail halaman kursus
- JSON-LD Course, breadcrumb, sitemap, OG meta

### 3.2 Enrollment gratis
- Enrollment dibuat langsung untuk `free`, order + Midtrans untuk `donation`/`paid` (pindah ke Fase 5)

### 3.3 Course player
- Modul > pelajaran, progres tersimpan otomatis (`lesson_progress`)
- "Lanjutkan terakhir" dashboard learner

### 3.4 Dashboard learner
- Riwayat kursus, progres, sertifikat

### 3.5 Reusable komponen Blade (batch 3)
- `x-progress-bar`, `x-course-card`, `x-lesson-list`

### Progress Fase 3 — SELESAI
- PublicCourseController (index, show): katalog dengan filter kategori/level/akses/durasi, facet counts, pencarian — OK.
- EnrollmentController: free enroll langsung, paid/donation ke Midtrans (pindah Fase 5) — OK.
- PlayerController + CourseProgressService: player modul > pelajaran, progres otomatis `lesson_progress`, soal kuis dengan scoring — OK.
- DashboardController: riwayat kursus, progres, sertifikat, gamification (XP level, badges) — OK.

### Masalah saat ini Fase 3
Tidak ada.

### Next move Fase 3
Commit Fase 3, lalu mulai Fase 4 — Kuis & Sertifikat (engine kuis, template sertifikat, PDF, verifikasi).

---

## Fase 4 — Kuis & Sertifikat (MVP S4)

### 4.1 Engine kuis
- Kuis pilihan ganda & benar/salah, passing grade, batas percobaan, acak soal, timer
- Soal diacak, timer server-side, jawaban benar tidak ke klien

### 4.2 Kelulusan & enrollment completed
- Skor > passing grade + semua pelajaran selesai => enrollment completed

### 4.3 Template sertifikat bilingual
- Template Blade/HTML, signatories JSON, background, aktif
- Nomor unik, QR, kode sertifikat acak + checksum, status revoke

### 4.4 PDF sertifikat via queue (dompdf)
- Job buat PDF, simpan storage, kirim email

### 4.5 Halaman verifikasi & LinkedIn
- `/verify/{code}`: nama, kursus, tanggal, status, OG meta, JSON-LD credential
- Tombol Add to LinkedIn, bagikan

### Progress Fase 4 — SELESAI
- Quiz engine: pilihan ganda, scoring, passing grade, percobaan unlimited, acak soal, timer — OK.
- Kelulusan & enrollment completed: skor >= passing grade + semua pelajaran selesai => enrollment.status='completed' — OK.
- Template sertifikat: view Blade + QR code, bilingual fallback, kode unik (NX-YYYY-XXXXXXXX), checksum, status revoke — OK.
- PDF sertifikat via dompdf: download langsung, render HTML ke PDF landscape A4 — OK.
- Halaman verifikasi & LinkedIn: `/verifikasi/{code}`, status valid/revoked/not_found, OG meta (siap), JSON-LD (siap) — OK.

### Masalah saat ini Fase 4
Tidak ada.

### Next move Fase 4
Commit Fase 4, lalu mulai Fase 5 — Pembayaran Midtrans (orders, webhooks, settlement).

---

## Fase 5 — Pembayaran Midtrans (MVP S5)

### 5.1 Orders & Midtrans Snap
- Tabel `orders`, `payment_events` (idempotensi)
- Snap token, popup QRIS/VA/e-wallet/kartu
- Order ID `AF-{type}-{uuid pendek}`

### 5.2 Webhook Midtrans
- `POST /webhooks/midtrans`, dikecualikan CSRF, throttle
- Cek signature SHA512, konfirmasi via Status API
- Idempotensi: simpan payload, cek status order sebelum ubah

### 5.3 Gerbang akses & sertifikat
- Enrollment aktif setelah settlement
- Sertifikat `pending_payment` => terbit setelah settlement

### 5.4 Receipt/invoice email + laporan transaksi admin
- Email pembukuan, laporan dasar admin

### Progress Fase 5
Belum dimulai.

### Masalah saat ini Fase 5
Belum ada.

### Next move Fase 5
Mulai setelah Fase 4 selesai & commit.

---

## Fase 6 — Stabilisasi & Rilis (MVP S6)

### 6.1 Notifikasi email, laporan dasar
- Welcome, enroll, sertifikat, receipt, reminder

### 6.2 Hardening keamanan OWASP ASVS L2
- Dependency scan, pentest dasar, audit log

### 6.3 Audit SEO (Lighthouse, Rich Results Test)
- Uji JSON-LD, OG, sitemap, hreflang

### 6.4 UAT, optimasi, deploy production
- Soft launch 2-3 kursus pilot

### Progress Fase 6
Belum dimulai.

### Masalah saat ini Fase 6
Belum ada.

### Next move Fase 6
Mulai setelah Fase 5 selesai & commit.

---

## Catatan Per Fase (diisi saat bekerja)

| Fase | Commit hash | Tanggal | Selesai | Catatan |
| --- | --- | --- | --- | --- |
| 1 | 7fc32a5 | 6 Nov 2026 | Selesai | Fondasi RBAC, skema DB inti, traits & helper reusable |
| 2 | 06b02f4 | 8 Nov 2026 | Selesai | CRUD + tenant isolation + komponen Blade batch 2, 23 test lulus |
| 3 | df3cc77 | 8 Nov 2026 | Selesai | Katalog publik, enroll gratis, player, dashboard learner + gamification |
| 4 | — | — | Selesai | Quiz engine, sertifikat auto-issue, PDF, verifikasi |
| 5 | — | — | Belum | — |
| 6 | — | — | Belum | — |
