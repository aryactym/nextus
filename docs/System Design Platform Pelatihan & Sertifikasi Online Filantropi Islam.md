# System Design: Platform Pelatihan & Sertifikasi Online Filantropi Islam

**Working name:** Akademi Filantropi (placeholder) | **Versi:** 0.2 | **Tanggal:** 6 Nov 2026 **Stack:** Laravel, PHP 8.4, MySQL, Blade, Tabler UI | **Bahasa:** Indonesia & Inggris | **Pembayaran:** Midtrans

## 1. Ringkasan

Platform e-learning interaktif untuk lembaga filantropi Islam: katalog kursus, course player, kuis, sertifikat digital yang bisa diverifikasi dan ditambahkan ke LinkedIn, serta model akses fleksibel (gratis, donasi, berbayar) lewat Midtrans. Pola referensi dari platform seperti KayaConnect dan DisasterReady: katalog kursus per topik, belajar mandiri, sertifikat penyelesaian, akses terbuka untuk publik.

| Tujuan | Indikator |
| --- | --- |
| Akses pelatihan luas untuk relawan, amil, mitra, publik | Jumlah pendaftar & kursus selesai |
| Sertifikat kredibel & dapat diverifikasi | Verifikasi publik via QR/URL, tombol Add to LinkedIn |
| Pelatihan sekaligus kanal penggalangan dana | Total donasi dari kursus & konversi donasi |
| Operasional ringan untuk tim kecil | Admin bisa publish kursus tanpa developer |

## 2. Peran Pengguna

| Peran | Kemampuan utama |
| --- | --- |
| Guest | Lihat katalog, preview kursus, ganti bahasa, verifikasi sertifikat |
| Learner | Daftar, belajar, kuis, unduh sertifikat, donasi/bayar, riwayat transaksi |
| Instructor / Content Manager | Kelola kursus, modul, materi, kuis; lihat progres peserta |
| Admin | Kelola user, kategori, harga, template sertifikat, transaksi, laporan |
| Super Admin | Pengaturan sistem, kunci Midtrans, peran, audit log |

## 3. Model Akses & Monetisasi

Setiap kursus punya dua pengaturan independen: **tipe akses** dan **tipe sertifikat**.

| Field | Nilai |
| --- | --- |
| `access_type` | `free`, `donation` (donasi minimum untuk masuk), `paid` |
| `certificate_type` | `free`, `donation`, `paid` |
| Pendukung | `min_amount`, `suggested_amounts` (JSON), `allow_custom_amount`, `currency` |

**Preset yang disiapkan di admin:**

| Preset | Akses | Sertifikat | Skenario |
| --- | --- | --- | --- |
| P1 | Gratis | Gratis | Kursus pengantar / kampanye awareness |
| P2 | Gratis + ajakan donasi sukarela | Gratis | Donasi tidak menghalangi akses |
| P3 | Via donasi | Gratis | Pelatihan sebagai program penggalangan dana |
| P4 | Gratis | Via donasi | Belajar gratis, sertifikat unlock setelah donasi |
| P5 | Berbayar | Gratis | Kursus premium |
| P6 | Berbayar | Via donasi | Kursus berbayar, sertifikat dengan donasi tambahan |

> Asumsi: permintaan 'pelatihan bayar tapi sertifikat bayar donasi' dipetakan ke P6, dan 'gratis, sertifikat via donasi' ke P4. Konfirmasi di bagian Keputusan Terbuka.

**Prinsip desain pembayaran**

- Satu tabel `orders` untuk semua transaksi, dibedakan oleh `order_type` (`enrollment`, `certificate`, `donation`) dan `nature` (`payment` atau `donation`).
- Donasi menghasilkan bukti donasi, pembayaran menghasilkan invoice. Keduanya lewat Midtrans Snap.
- Akses/sertifikat dibuka hanya setelah status `settlement` (atau `capture` untuk kartu) dari webhook, bukan dari redirect browser.
- Catatan syariah & legal: pisahkan akun/pos dana infak-sedekah dari zakat, dan hindari menjadikan dana zakat sebagai syarat akses. Konfirmasi akad dan perlakuan pajak dengan Dewan Syariah/legal.

## 4. Arsitektur

```
[Browser: HTML server-side dari Blade + Tabler UI + Alpine.js]
          |
       [Nginx] --> [Laravel 12 / PHP 8.4-FPM]  (modular monolith)
                      |-- Modul: Auth, Catalog, Learning, Assessment,
                      |          Certificate, Payment, Organization, Admin, Report
                      |-- Queue worker: PDF sertifikat, email, proses webhook
                      |-- Scheduler: reminder, expire order, cleanup
   MySQL 8 (utama) | Redis (cache, queue, session)
   Object storage S3-compatible (media, PDF sertifikat)
   Eksternal: Midtrans Snap, LinkedIn Add-to-Profile (URL), Google OAuth,
              SMTP/Resend, video host (YouTube/Vimeo/Bunny)
```

**Keputusan arsitektur**

| Keputusan | Alasan |
| --- | --- |
| Modular monolith (folder per domain di `app/Domain/*`) | Tim kecil, deploy sederhana, mudah dipecah nanti |
| Blade (SSR) + Alpine.js untuk interaksi | Tanpa Livewire/SPA: halaman dirender server, interaksi (kuis, progres, player) lewat form biasa + fetch/AJAX ringan; ramah SEO dan mudah dioperasikan |
| Queue Redis untuk tugas berat | PDF dan email tidak memblokir request |
| Media di object storage | Tidak membebani server aplikasi |
| UUID sebagai primary key publik | Aman untuk URL, verifikasi sertifikat |

## 5. Stack & Paket

| Kebutuhan | Pilihan |
| --- | --- |
| Framework | Laravel 12 (PHP 8.4), MySQL 8, Redis |
| UI | Blade (SSR), Tabler UI (Bootstrap 5), Alpine.js + fetch API untuk interaksi ringan, Vite |
| Auth | Laravel Breeze/Fortify, `laravel/socialite` (Google), verifikasi email |
| RBAC | `spatie/laravel-permission` |
| i18n | `mcamara/laravel-localization` (prefix `/id`, `/en`), `spatie/laravel-translatable` (konten JSON) |
| Media | `spatie/laravel-medialibrary` |
| PDF sertifikat | `barryvdh/laravel-dompdf` (awal), opsi `spatie/laravel-pdf`/Browsershot jika desain kompleks |
| QR | `simplesoftwareio/simple-qrcode` |
| Pembayaran | `midtrans/midtrans-php` |
| Audit | `spatie/laravel-activitylog` |
| Kualitas | Pest, Larastan, Pint, GitHub Actions, composer audit, npm audit, Dependabot |
| Observability | Sentry, Laravel Pulse |

## 6. Modul Fungsional

| Modul | Fitur MVP | Pasca-MVP |
| --- | --- | --- |
| Auth & Profil | Register/login email & Google, profil, pilih bahasa | SSO mitra, akun organisasi |
| Katalog | Kategori, pencarian, filter, halaman detail kursus | Learning path, rekomendasi |
| Course Player | Modul > pelajaran (video, teks, PDF), progres, lanjutkan terakhir | Live session, H5P/SCORM |
| Assessment | Kuis pilihan ganda & benar/salah, passing grade, batas percobaan, acak soal, timer | Essay, matching, peer review, banked questions |
| Sertifikat | Template bilingual, nomor unik, PDF, QR, halaman verifikasi, Add to LinkedIn | Open Badges 3.0, kedaluwarsa & resertifikasi |
| Pembayaran | Midtrans Snap, donasi, webhook, order, receipt email | Kupon, subscription, refund workflow |
| Admin | CRUD kursus, user, transaksi, template sertifikat, laporan dasar | Analitik lanjutan, ekspor terjadwal |
| Notifikasi | Email: welcome, enroll, sertifikat, receipt, reminder | WhatsApp, push (PWA) |

## 7. Model Data (inti)

Semua tabel memakai `id` BIGINT internal + `uuid` publik, timestamps, soft delete bila relevan. Kolom teks yang diterjemahkan disimpan JSON (`{id: ..., en: ...}`).

| Tabel | Kolom penting |
| --- | --- |
| `users` | name, email, password, locale, linkedin\_url (opsional), email\_verified\_at |
| `categories` | name (JSON), slug (JSON), sort |
| `courses` | title, summary, description (JSON), slug, category\_id, level, duration\_minutes, thumbnail, access\_type, certificate\_type, min\_amount, suggested\_amounts, passing\_grade, status (draft/published/archived), published\_at, created\_by |
| `course_requirements` | `prerequisite_course_id` (single course prerequisite), `prerequisite_category_id` (wajib selesai semua course di kategori tertentu), `min_grade`, `pendidikan_id`, `min_age`, `max_age`, `notes` |
| `course_requirements` | `prerequisite_course_id` (course prasyarat tunggal), `prerequisite_category_id` (wajib selesai semua kursus di kategori tertentu), `min_grade`, `pendidikan_id`, `min_age`, `max_age`, `notes` |
| `course_modules` | course\_id, title (JSON), sort |
| `lessons` | module\_id, title (JSON), type (video/text/pdf/quiz), content (JSON), video\_url, duration, is\_preview, sort |
| `enrollments` | user\_id, course\_id, status (active/completed), progress\_percent, enrolled\_at, completed\_at, order\_id (nullable) |
| `lesson_progress` | enrollment\_id, lesson\_id, completed\_at, last\_position |
| `quizzes` | course\_id/lesson\_id, title, passing\_score, max\_attempts, time\_limit, shuffle |
| `questions` / `question_options` | quiz\_id, type, text (JSON), explanation (JSON), points / question\_id, text (JSON), is\_correct |
| `quiz_attempts` / `attempt_answers` | enrollment\_id, quiz\_id, score, passed, started\_at, finished\_at / attempt\_id, question\_id, answer |
| `certificate_templates` | name, layout (blade/HTML), signatories (JSON), background, active |
| `certificates` | uuid, code (unik, mis. AF-2026-000123), enrollment\_id, user\_id, course\_id, issued\_at, pdf\_path, status (pending\_payment/issued/revoked), locale |
| `orders` | uuid, midtrans\_order\_id, user\_id, course\_id, order\_type, nature, amount, currency, status, snap\_token, payment\_type, paid\_at, expired\_at |
| `payment_events` | order\_id, payload (JSON), signature\_valid, received\_at (idempotensi & audit) |
| `settings`, `activity_log` | konfigurasi organisasi, jejak audit |

## 8. Alur Utama

**8.1 Enrollment**

1. Learner buka kursus, klik Daftar.
2. `free`: enrollment dibuat langsung.
3. `donation`/`paid`: buat `orders` (pending), minta Snap token, tampilkan Snap popup.
4. Webhook Midtrans valid dan `settlement`: order jadi `paid`, enrollment diaktifkan, email dikirim.

**8.2 Belajar & kelulusan**

1. Selesaikan pelajaran (progres tersimpan otomatis), lalu kuis akhir.
2. Skor di atas passing grade dan semua pelajaran wajib selesai: enrollment `completed`.
3. **Sistem cek requirement prerequisite** (mulai S6 stabilisasi):
   - Jika course punya `prerequisite_course_id`: user harus lulus course prasyarat tersebut (dengan nilai minimal jika ada).
   - Jika course punya `prerequisite_category_id`: user harus menyelesaikan **semua** kursus di kategori prasyarat tersebut sebelum bisa daftar.
   - Jika ada `pendidikan_id`: user harus memiliki pendidikan level minimal.
4. Sistem cek `certificate_type`:
   - `free`: sertifikat langsung dibuat (job queue).
   - `donation`/`paid`: sertifikat `pending_payment`, tampilkan CTA donasi/bayar, terbit setelah settlement.

**8.3 Sertifikat & LinkedIn**

1. Job membuat PDF (bilingual sesuai locale), menyimpan ke storage, mengirim email.
2. Halaman publik `/verify/{code}` menampilkan nama, kursus, tanggal, status.
3. Tombol **Add to LinkedIn** membuka URL Add-to-Profile:

```
https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME
  &name={judul sertifikat}
  &organizationId={ID halaman LinkedIn lembaga}
  &issueYear={yyyy}&issueMonth={m}
  &certUrl={https://domain/verify/{code}}
  &certId={code}
```

Tanpa API approval; butuh halaman LinkedIn lembaga (organizationId). Tambahkan juga tombol bagikan dan Open Graph meta di halaman verifikasi agar tampil rapi saat dibagikan.

## 9. Integrasi Midtrans

| Aspek | Rancangan |
| --- | --- |
| Metode | Snap (popup): QRIS, Virtual Account, e-wallet, kartu |
| Order ID | `AF-{type}-{uuid pendek}` unik per transaksi |
| Webhook | `POST /webhooks/midtrans`, dikecualikan dari CSRF, throttle |
| Verifikasi | Cek signature: SHA512(order\_id + status\_code + gross\_amount + ServerKey); lalu konfirmasi status via Midtrans Status API untuk transaksi penting |
| Idempotensi | Simpan tiap payload di `payment_events`; proses ulang aman (cek status order sebelum ubah) |
| Status | pending, settlement/capture, deny, cancel, expire, refund |
| Expire | Scheduler menandai order pending kedaluwarsa dan melepas hold |
| Donasi nominal | Preset (mis. 25rb/50rb/100rb) + nominal bebas, validasi minimum |
| Lingkungan | Sandbox untuk staging, kunci di `.env`, tidak pernah di repo |
| Rekonsiliasi | Laporan harian order vs dashboard Midtrans (Fase 2 otomatis) |

## 10. Dwibahasa (ID/EN)

- URL berprefix locale (`/id/...`, `/en/...`), deteksi awal dari browser, preferensi tersimpan di profil.
- UI string via `lang/id` dan `lang/en`; konten kursus via kolom JSON translatable dengan fallback ke bahasa lain bila terjemahan kosong.
- Admin menandai status terjemahan per kursus (lengkap/sebagian) dan hanya kursus berterjemahan lengkap tampil di bahasa tersebut (opsi).
- Sertifikat, email, dan receipt tersedia dalam kedua bahasa; learner memilih bahasa sertifikat.
- Tag `hreflang` dan sitemap per bahasa untuk SEO.

## 11. SEO, Schema & Open Graph

Semua halaman publik dirender server-side lewat Blade, sehingga konten langsung terbaca crawler tanpa JavaScript; area learner dan admin diberi `noindex`.

**Praktik SEO**

| Area | Rancangan |
| --- | --- |
| URL | Slug bersih per bahasa (`/id/kursus/{slug}`, `/en/courses/{slug}`), 301 bila slug berubah |
| Meta | Komponen Blade `<x-seo>`: title unik (maks. 60 karakter), meta description (maks. 160), canonical, robots |
| Hreflang | `id`, `en`, dan `x-default` per halaman, hanya bila terjemahan tersedia |
| Sitemap & robots | `spatie/laravel-sitemap` (index per bahasa, diperbarui saat kursus dipublish); `robots.txt` memblokir `/admin`, `/dashboard`, `/checkout` |
| Performa | Target Core Web Vitals hijau: lazy loading, gambar WebP/AVIF dengan dimensi eksplisit, Vite minify, response cache untuk katalog, CDN |
| Konten | Satu H1 per halaman, hierarki H2/H3, alt text, breadcrumb, tautan internal ke kursus terkait |
| Pagination & filter | Canonical self-referencing; hasil pencarian dan kombinasi filter `noindex` |

**Structured data (JSON-LD, via `spatie/schema-org`)**

| Halaman | Tipe schema | Properti kunci |
| --- | --- | --- |
| Semua | `Organization`/`NGO`, `WebSite` + `SearchAction` | name, url, logo, sameAs (LinkedIn, Instagram, YouTube) |
| Semua halaman dalam | `BreadcrumbList` | itemListElement |
| Katalog | `ItemList` | daftar `Course` |
| Detail kursus | `Course` + `CourseInstance` (courseMode online) | name, description, provider, inLanguage, timeRequired, offers (price 0 atau nominal, priceCurrency IDR) |
| Verifikasi sertifikat | `EducationalOccupationalCredential` | name, identifier (kode), recognizedBy, url |
| FAQ / artikel (Fase 2) | `FAQPage`, `Article` | pertanyaan-jawaban; headline, datePublished, author |

Validasi dengan Rich Results Test dan Schema Markup Validator; tes otomatis di CI memastikan tag dan JSON-LD tidak hilang.

**Open Graph & kartu sosial**

| Meta | Nilai |
| --- | --- |
| Dasar | `og:title`, `og:description`, `og:url`, `og:type` (`website`/`article`), `og:site_name` |
| Bahasa | `og:locale` (`id_ID`/`en_US`) + `og:locale:alternate` |
| Gambar | `og:image` 1200x630 per kursus; sertifikat memakai gambar OG otomatis (nama kursus + logo) |
| Twitter/X | `twitter:card` = `summary_large_image` |
| LinkedIn | Memakai tag OG yang sama; dicek lewat LinkedIn Post Inspector |

Halaman verifikasi sertifikat individual memakai OG agar rapi saat dibagikan, tetapi diberi `noindex, nofollow` demi privasi peserta (nama tampil sesuai persetujuan).

## 12. Keamanan (OWASP) & Kepatuhan

Baseline: **OWASP Top 10 (2021)** untuk desain dan code review, **OWASP ASVS Level 2** sebagai checklist rilis.

| OWASP | Kontrol di platform |
| --- | --- |
| A01 Broken Access Control | Policy/Gate per resource, middleware peran, deny by default; cek kepemilikan enrollment, sertifikat, order (anti-IDOR, UUID di URL); signed URL untuk file privat |
| A02 Cryptographic Failures | HTTPS + HSTS, hash password Argon2id/bcrypt, enkripsi kolom sensitif, secret hanya di env/secret manager, tidak menyimpan data kartu, backup terenkripsi |
| A03 Injection | Eloquent/query binding (tanpa string concat), validasi FormRequest, escaping Blade `{{ }}` (hindari `{!! !!}`), sanitasi HTML materi dengan HTMLPurifier, CSP |
| A04 Insecure Design | Threat modeling per modul (pembayaran, sertifikat, kuis), state machine order, rate limit, batas percobaan kuis |
| A05 Security Misconfiguration | `APP_DEBUG=false`, security headers (CSP, X-Content-Type-Options, frame-ancestors, Referrer-Policy, Permissions-Policy), hardening Nginx/PHP/MySQL, user DB least privilege |
| A06 Vulnerable Components | `composer audit` dan `npm audit` di CI, Dependabot, pembaruan berkala, versi paket dikunci |
| A07 Identification & Auth Failures | Kebijakan password (NIST), rate limit login, 2FA TOTP untuk admin, regenerasi sesi, cookie Secure/HttpOnly/SameSite, verifikasi email, token reset berumur pendek |
| A08 Software & Data Integrity | Verifikasi signature webhook Midtrans, pipeline CI/CD terproteksi, SRI untuk script CDN, tanpa unserialize input pengguna |
| A09 Logging & Monitoring Failures | Activity log (login gagal, perubahan peran, transaksi, aksi admin), Sentry + alert, data sensitif tidak masuk log |
| A10 SSRF | Embed video hanya dari allowlist host, server tidak mengambil URL dari input pengguna |
| CSRF & upload | Token CSRF di semua form (webhook dikecualikan, dilindungi signature); upload: allowlist ekstensi + MIME, nama acak, simpan di luar webroot |
| Kuis & sertifikat | Soal diacak, timer server-side, jawaban benar tidak dikirim ke klien; kode sertifikat acak + checksum, status revoke |
| Data pribadi | Patuh UU PDP (consent, kebijakan privasi, hapus akun); pertimbangkan GDPR untuk peserta luar negeri |
| Operasional | Backup MySQL harian dan uji restore, HTTPS di semua lingkungan, akses server via SSH key + least privilege |

## 13. Non-Fungsional & Deployment

| Aspek | Target |
| --- | --- |
| Performa | Halaman katalog < 2 dtk (cache), video via CDN/host eksternal |
| Ketersediaan | 99,5% pada MVP, health check & alert |
| Skala awal | 5.000 user terdaftar, 500 concurrent belajar |
| Deployment | Docker/VPS (Nginx, PHP-FPM, MySQL, Redis), CI/CD GitHub Actions, staging + production |
| Aksesibilitas | Kontras Tabler, label form, caption video (target WCAG 2.1 AA bertahap) |
| Responsif | Mobile-first; mayoritas peserta lewat HP |

## 14. Roadmap

### 14.1 MVP: 3 Bulan Pertama (6 sprint x 2 minggu)

| Sprint | Fokus | Output utama |
| --- | --- | --- |
| S1 (M1-2) | Fondasi | Repo, CI, Laravel 12/PHP 8.4, layout Tabler, auth + Google, RBAC, kerangka i18n, skema DB inti, baseline keamanan OWASP (header, CSRF, rate limit), komponen Blade SEO (meta, OG, canonical), fondasi multi-organisasi (organization\_id, global scope, spatie teams) |
| S2 (M3-4) | CMS kursus | CRUD kategori/kursus/modul/pelajaran, konten ID/EN, upload media, alur draft-publish |
| S3 (M5-6) | Pengalaman belajar | Katalog & detail kursus (SSR + JSON-LD Course, breadcrumb, sitemap), enrollment gratis, course player, progres, dashboard learner |
| S4 (M7-8) | Kuis & sertifikat | Engine kuis, passing grade, template sertifikat bilingual, PDF via queue, QR, halaman verifikasi (OG meta + JSON-LD credential), **Add to LinkedIn** |
| S5 (M9-10) | Pembayaran | Midtrans Snap, matriks access/certificate type, webhook, orders, gerbang sertifikat, receipt/invoice email, laporan transaksi admin |
| S6 (M11-12) | Stabilisasi & rilis | Notifikasi email, laporan dasar, hardening keamanan (checklist OWASP ASVS L2, dependency scan, pentest dasar), audit SEO (Lighthouse, Rich Results Test), UAT, optimasi, deploy production, soft launch 2-3 kursus pilot |

**Definition of Done MVP**

- Learner bisa daftar, belajar, lulus kuis, dan mendapat sertifikat dalam ID atau EN.
- Keenam preset akses (P1-P6) berfungsi end-to-end di sandbox dan produksi.
- Sertifikat dapat diverifikasi publik dan ditambahkan ke LinkedIn.
- Admin dapat mempublikasikan kursus baru tanpa bantuan developer. Halaman publik lolos Rich Results Test dan checklist OWASP Top 10.

**Di luar MVP:** learning path, kupon, live session, essay/peer review, Open Badges, akun organisasi, aplikasi mobile.

### 14.2 Setelah MVP

| Fase | Periode | Cakupan |
| --- | --- | --- |
| Fase 2: Pertumbuhan | Bulan 4-6 | Learning path, cohort/batch & kelas terjadwal (link webinar), kupon/voucher, kuis lanjutan (essay, matching), email automation & reminder, halaman dampak donasi, analitik admin, rekonsiliasi Midtrans otomatis, portal organisasi (onboarding, anggota & peran, branding), aturan prasyarat penyelenggara |
| Fase 3: Kredensial & Skala | Bulan 7-9 | Open Badges 3.0 (opsional Credly), kedaluwarsa & resertifikasi, subdomain/custom domain dan Midtrans per organisasi, paket & kuota organisasi, bulk enroll, forum diskusi, gamifikasi (badge, streak), PWA, impor H5P/SCORM, REST API |
| Fase 4: Ekosistem | Bulan 10-12+ | SSO mitra, integrasi CRM/CDP donatur, WhatsApp notifikasi, rekomendasi kursus & bantuan terjemahan berbasis AI, marketplace konten mitra, aplikasi mobile/offline |

## 15. Multi-Organisasi (Multi-Tenancy) & Prasyarat Penyelenggara

Tujuan: platform tidak hanya dipakai satu lembaga. Ke depan, organisasi lain (lembaga filantropi, ormas, komunitas) bisa login ke portal organisasinya dan mengelola pelatihan sendiri, dengan syarat tertentu. MVP tetap berjalan sebagai satu organisasi, tetapi fondasi data dan kode dibuat **tenant-ready sejak S1** agar tidak perlu refactor besar.

### 15.1 Model multi-tenancy

| Aspek | Keputusan |
| --- | --- |
| Pola | Single database, shared schema: kolom `organization_id` pada tabel milik organisasi, trait `BelongsToOrganization` + global scope Eloquent |
| Alternatif | Database per organisasi (mis. `stancl/tenancy`) hanya bila ada klien besar yang butuh isolasi penuh; jalur migrasi tetap terbuka |
| Identitas | Satu akun global per orang; keanggotaan di `organization_user`; satu orang boleh anggota beberapa organisasi dan berpindah lewat org switcher |
| Peran | `spatie/laravel-permission` mode teams (team = organisasi) |
| Resolusi tenant | Awal: path `/o/{slug}`; Fase 3: subdomain `{slug}.domain` dan custom domain. Katalog publik pusat menampilkan kursus publik semua organisasi |
| Branding | Per organisasi: logo, warna, bahasa default, template dan penandatangan sertifikat, nama penerbit |
| Sertifikat & LinkedIn | `certificates.organization_id` sebagai penerbit; `linkedin_org_id` per organisasi dipakai di tombol Add to LinkedIn |
| Pembayaran | Rekomendasi: kredensial Midtrans per organisasi (terenkripsi) agar dana langsung ke lembaga; alternatif platform menampung lalu menyalurkan (beban akuntansi dan regulasi lebih besar) |
| SEO | Canonical, sitemap, dan JSON-LD `Organization` per tenant; tiap kursus publik punya satu URL kanonik |
| Paket & kuota | Opsional Fase 3: batas kursus, peserta, dan penyimpanan per organisasi |

**Isolasi data (terkait OWASP A01)**

- Global scope wajib di semua model tenant, ditambah Policy yang mengecek `organization_id`.
- Unique constraint gabungan (mis. slug unik per organisasi, bukan global).
- Cache key, path storage (`org/{uuid}/...`), dan queue job membawa konteks organisasi.
- Tes otomatis lintas-tenant di CI: pengguna organisasi A tidak bisa membaca atau mengubah data organisasi B.
- Audit log selalu mencatat `organization_id`.

### 15.2 Peran tingkat organisasi

| Peran | Kemampuan |
| --- | --- |
| Platform Super Admin | Semua organisasi, aturan prasyarat, persetujuan penyelenggara, pengaturan global |
| Org Owner | Kelola organisasi, anggota, branding, kredensial Midtrans |
| Org Admin | Kelola anggota, kursus, peserta, dan laporan organisasi |
| Org Instructor | Buat/ubah materi dan kuis, kelola kelas yang ditugaskan (jika memenuhi prasyarat) |
| Org Reviewer | Tinjau konten sebelum publish |
| Org Member | Belajar sebagai peserta internal organisasi |

### 15.3 Prasyarat untuk mengelola pelatihan

Konsep: **Kapabilitas Penyelenggara**. Untuk mengelola pelatihan tertentu (mengadopsi materi, membuka kelas, menerbitkan sertifikat), organisasi atau instrukturnya harus lebih dulu menyelesaikan pelatihan prasyarat dan memegang sertifikat yang masih berlaku.

| Jenis pelatihan | Aturan |
| --- | --- |
| Kursus milik sendiri (`ownership = own`) | Bebas dibuat organisasi; prasyarat opsional sesuai kebijakan platform (mis. wajib lulus 'Dasar Pembuatan Kursus') |
| Kursus berlisensi / template platform (`ownership = licensed`) | Organisasi hanya bisa mengelolanya setelah prasyarat terpenuhi |

| Elemen aturan | Penjelasan | Contoh |
| --- | --- | --- |
| Cakupan (`scope`) | `user`: pengelola harus lulus; `organization`: minimal N anggota bersertifikat | Min. 2 fasilitator bersertifikat 'Dasar Fasilitasi (ToT)' |
| Logika | AND antar grup, OR di dalam grup | ToT Dasar DAN Safeguarding; atau salah satu dari ToT Dasar / Fasilitator Nasional |
| Masa berlaku | Sertifikat tidak dicabut/kedaluwarsa; opsional usia maksimum | Maks. 24 bulan |
| Persetujuan | Otomatis, atau perlu persetujuan Super Admin | Pelatihan sensitif butuh review manual |
| Tindakan yang digerbang | Adopsi materi, buka kelas/cohort, publish, terbitkan sertifikat | Policy `manage`, `adopt`, `publish` |

**Alur**

1. Org Admin membuka **Katalog Penyelenggara** (pelatihan yang bisa dikelola) dan melihat syarat tiap pelatihan beserta progresnya, mis. 'Syarat 1 dari 2 terpenuhi'.
2. Anggota mengambil pelatihan prasyarat lewat katalog biasa (gratis, donasi, atau berbayar sesuai model akses P1-P6).
3. Saat sertifikat terbit, evaluator menghitung ulang kelayakan; status menjadi `eligible`.
4. Bila perlu persetujuan, organisasi mengajukan dan Super Admin menyetujui; bila tidak, status langsung `active`.
5. Organisasi mengelola pelatihan: adopsi materi, atur harga dan model akses, jadwal, peserta, laporan.
6. Jika sertifikat prasyarat dicabut atau kedaluwarsa, otorisasi menjadi `suspended` setelah masa tenggang (mis. 30 hari) dan pengelola diberi notifikasi.

Status otorisasi: `not_eligible` > `eligible` > `pending_approval` > `active` > `suspended` / `revoked`.

**Implementasi**

- `OrganizerEligibilityService::check(Organization, ?User, Course)` mengembalikan daftar syarat terpenuhi dan belum; dipakai Policy, UI progres, dan job harian.
- Event `CertificateIssued`, `CertificateRevoked`, dan `MemberRemoved` memicu evaluasi ulang otorisasi terkait.
- Validasi DAG saat admin menyimpan aturan, untuk mencegah prasyarat berputar (A butuh B, B butuh A).
- Perubahan aturan tidak mencabut otorisasi yang sudah `active` kecuali ditetapkan eksplisit; tercatat di audit log.

### 15.4 Tambahan model data

| Tabel | Kolom penting |
| --- | --- |
| `organizations` | uuid, name, slug, type, status, logo, brand (JSON), default\_locale, linkedin\_org\_id, custom\_domain, midtrans\_config (terenkripsi), settings |
| `organization_user` | organization\_id, user\_id, role, status, invited\_at, joined\_at |
| `course_organizer_requirements` | course\_id (pelatihan yang dikelola), required\_course\_id, scope, min\_certified, group\_no, max\_certificate\_age\_months, requires\_approval, active |
| `organizer_authorizations` | uuid, organization\_id, user\_id (nullable), course\_id, status, evidence (JSON id sertifikat), approved\_by, approved\_at, expires\_at, suspended\_at |
| `course_deployments` | organization\_id, source\_course\_id, source\_version, status, branding\_override, access\_type, certificate\_type, published\_at |
| Kolom tambahan | `courses`: organization\_id, ownership (own/licensed), visibility (public/org\_only/unlisted); `certificates`: organization\_id, expires\_at; `orders` dan `enrollments`: organization\_id |

### 15.5 Tahapan multi-organisasi

| Tahap | Cakupan |
| --- | --- |
| MVP (S1) | Tenant-ready: tabel `organizations` dengan satu organisasi default, `organization_id` + global scope, spatie teams aktif, resolver tenant mode tunggal, tes isolasi |
| Fase 2 (bulan 4-6) | Portal organisasi (onboarding dan verifikasi, anggota dan peran, branding, org switcher), Katalog Penyelenggara, aturan prasyarat + evaluator + persetujuan, adopsi kursus berlisensi, laporan per organisasi |
| Fase 3 (bulan 7-9) | Subdomain/custom domain, Midtrans per organisasi, paket dan kuota, bulk enroll, white-label |
| Fase 4 (bulan 10-12+) | SSO organisasi (SAML/OIDC), API per organisasi, marketplace konten antar organisasi |

## 16. Tim, Risiko, & Keputusan Terbuka

**Tim minimal MVP:** 1 tech lead/backend Laravel, 1 full-stack, 1 UI/UX (paruh waktu), 1 product owner/konten, 1 QA (paruh waktu).

| Risiko | Mitigasi |
| --- | --- |
| Konten kursus belum siap saat rilis | Mulai produksi konten sejak S1, target 3 kursus pilot |
| Terjemahan ID/EN lambat | Prioritaskan satu bahasa dulu per kursus, fallback otomatis |
| Webhook Midtrans gagal/duplikat | Idempotensi, simpan payload, Status API sebagai cadangan |
| Persepsi sertifikat berbayar vs donasi | Wording jelas ('donasi untuk membuka sertifikat'), review Dewan Syariah |
| Beban video | Pakai host eksternal/CDN, bukan server aplikasi |

**Keputusan terbuka**

1. Konfirmasi pemetaan P4 dan P6, serta apakah nominal donasi minimum tetap atau bebas.
2. Status sertifikat: penyelesaian kursus (completion) atau kompetensi yang diakui (mis. LSP/BNSP, jika ya jalur terpisah).
3. Halaman LinkedIn lembaga dan organizationId tersedia?
4. Host video: YouTube/Vimeo/Bunny atau self-host.
5. Kebijakan refund dan penandatangan sertifikat (pejabat, tanda tangan digital).
6. Nama brand, domain, dan kebutuhan white-label untuk lembaga mitra.
7. Peran dana zakat dalam program ini (ya/tidak) setelah arahan Dewan Syariah.

**Keputusan terbuka tambahan (multi-organisasi)**

- Pelatihan mana yang menjadi prasyarat awal (mis. Dasar Fasilitasi/ToT) dan pelatihan mana yang berstatus berlisensi?
- Syarat berlaku per orang, per organisasi, atau keduanya; berapa minimal anggota bersertifikat?
- Apakah sertifikat prasyarat dari organisasi lain diakui, atau hanya dari penerbit tertentu?
- Persetujuan manual Super Admin untuk semua pelatihan atau hanya yang sensitif?
- Dana donasi/pembayaran: Midtrans per organisasi, atau platform menampung lalu menyalurkan?
- Model komersial untuk organisasi: gratis, paket langganan, atau bagi hasil?
- Masa tenggang saat sertifikat prasyarat kedaluwarsa atau dicabut (usulan 30 hari)?
