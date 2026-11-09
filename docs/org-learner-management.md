# Fitur Manajemen Pembelajaran Organisasi

**Status:** Konsep / Belum diimplementasikan  
**Tanggal:** November 2026

---

## 1. Latar Belakang

Saat ini Nextus beroperasi sebagai platform satu organisasi (single-tenant). Setiap user punya `organization_id` langsung di tabel `users`, dan semua query di-scope otomatis ke organisasi aktif via `BelongsToOrganization` trait + session.

Infrastruktur multi-organisasi sudah ada di level database — tabel `organizations`, kolom `organization_id` di users/courses/categories, dan tabel pivot `organization_user` (belum diaktifkan). Yang belum ada: role "Admin Organisasi" dan antarmuka untuk mengelola peserta serta memantau capaian di bawah satu organisasi.

---

## 2. Tujuan Fitur

1. **Admin organisasi mitra** bisa menambahkan dan mengelola akun peserta di bawah organisasinya sendiri — termasuk import massal via CSV.
2. **Admin organisasi mitra** bisa memantau capaian belajar seluruh peserta di bawah organisasinya: secara total, per topik/kategori, dan per judul kursus.
3. Platform operator (Super Admin) tetap bisa melihat semua organisasi.

---

## 3. Konsep Role

### Role baru: `Org Admin`

Tambahkan di PermissionSeeder dan DatabaseSeeder.

| Permission | Org Admin | Admin | Super Admin |
|---|:---:|:---:|:---:|
| `manage users` (scoped ke org sendiri) | ✅ | ✅ | ✅ |
| `manage org learners` (import + kelola peserta) | ✅ | — | ✅ |
| `view org reports` (laporan capaian org) | ✅ | ✅ | ✅ |
| `manage courses` | — | ✅ | ✅ |
| `manage payments` | — | ✅ | ✅ |
| `manage settings` | — | ✅ | ✅ |

**Prinsip penting:** Org Admin hanya bisa melihat dan mengelola data di dalam `organization_id`-nya sendiri. Tidak bisa lintas organisasi.

---

## 4. Struktur Data

### 4.1 Yang sudah ada dan langsung bisa dipakai

```
organizations
  └── users (via users.organization_id FK)
        └── enrollments (via enrollments.user_id)
              ├── courses (via enrollments.course_id)
              │     └── categories (via courses.category_id)  ← "per topik"
              ├── certificates
              ├── quiz_attempts (score, passed)
              └── lesson_progress (completed_at)
```

Semua data yang diperlukan untuk laporan sudah ada. Tidak perlu tabel baru untuk fitur inti.

### 4.2 Tabel `organization_user` (pivot) — aktifkan untuk membership

Tabel ini sudah ada di database tapi belum dipakai. Cocok untuk menyimpan metadata keanggotaan yang lebih kaya:

```
organization_user
  - organization_id
  - user_id
  - role        → 'member' | 'admin'    (role dalam konteks organisasi ini)
  - status      → 'active' | 'invited' | 'suspended'
  - invited_at
  - joined_at
```

**Opsi A — Tetap pakai direct FK (`users.organization_id`):**  
Lebih sederhana. Cocok untuk peserta yang hanya milik satu organisasi.

**Opsi B — Aktifkan pivot `organization_user`:**  
Lebih fleksibel. Cocok bila kelak satu user bisa bergabung ke lebih dari satu organisasi (misalnya konsultan lintas lembaga).

**Rekomendasi untuk sekarang: Opsi A** — pakai FK yang sudah ada, pivot diaktifkan nanti kalau benar-benar dibutuhkan.

---

## 5. Fitur Detail

### 5.1 Manajemen Peserta Organisasi

**URL:** `/org/learners`  
**Route name prefix:** `org.learners.*`

#### Daftar peserta
- Tabel peserta yang `organization_id`-nya sama dengan org admin yang login
- Kolom: nama, email, jabatan, tanggal bergabung, jumlah kursus aktif, jumlah kursus selesai
- Filter: nama/email, status (aktif/non-aktif)
- Tombol: Tambah manual, Import CSV, Nonaktifkan

#### Tambah peserta manual
Form: nama, email, password (atau kirim email undangan), jabatan, pendidikan.  
User dibuat dengan role `Learner` dan `organization_id` = org admin yang login.

#### Import CSV
- Upload file `.csv` dengan kolom: `name`, `email`, `password`, `nama_jabatan` (opsional), `kategori_jabatan` (opsional)
- Validasi per baris (email duplikat, format tidak valid) — tampilkan ringkasan error sebelum proses
- Proses via queued job agar tidak timeout untuk file besar
- Kirim email selamat datang per peserta (opsional, bisa dimatikan)

**Format CSV template yang disediakan:**
```csv
name,email,password,nama_jabatan,kategori_jabatan
Ahmad Fauzi,ahmad@contoh.org,Pass1234!,Staf Program,Amil
Siti Rahma,siti@contoh.org,Pass1234!,Relawan,,
```

#### Detail peserta
- Profil singkat
- Daftar kursus yang diikuti + progres
- Riwayat sertifikat

---

### 5.2 Dashboard Capaian Pembelajaran

**URL:** `/org/reports`  
**Route name prefix:** `org.reports.*`

#### Halaman utama — Ringkasan

Stat cards di bagian atas:

| Kartu | Data |
|---|---|
| Total peserta aktif | `COUNT(users) WHERE org_id = X AND status active` |
| Kursus diikuti (enrollment) | `COUNT(enrollments) JOIN users org` |
| Kursus diselesaikan | `COUNT(enrollments) WHERE completed_at NOT NULL` |
| Sertifikat diterbitkan | `COUNT(certificates) JOIN users org` |
| Rata-rata progres | `AVG(enrollments.progress_percent)` |

Filter periode (Litepicker date range, sudah ada di project) + filter kursus (Tom Select, juga sudah ada).

---

#### Tab / Seksi: Per Topik (Kategori)

Tabel atau bar chart sederhana:

| Topik | Peserta terdaftar | Selesai | Rata-rata progres |
|---|---|---|---|
| Manajemen Risiko | 24 | 18 | 87% |
| Keuangan Syariah | 31 | 20 | 72% |
| Kepemimpinan | 12 | 5 | 45% |

Query inti:
```sql
SELECT
    c.name as category_name,
    COUNT(DISTINCT e.user_id) as enrolled,
    SUM(CASE WHEN e.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed,
    ROUND(AVG(e.progress_percent)) as avg_progress
FROM enrollments e
JOIN users u ON e.user_id = u.id
JOIN courses co ON e.course_id = co.id
JOIN categories c ON co.category_id = c.id
WHERE u.organization_id = :org_id
GROUP BY c.id, c.name
ORDER BY enrolled DESC
```

---

#### Tab / Seksi: Per Judul Kursus

Tabel detail per kursus:

| Judul kursus | Topik | Peserta | Selesai | Rata-rata nilai kuis | Sertifikat |
|---|---|---|---|---|---|
| Manajemen Risiko Program | Manajemen | 24 | 18 | 82 | 18 |
| Laporan Keuangan Zakat | Keuangan | 31 | 20 | 74 | 19 |

Klik judul kursus → halaman drill-down daftar peserta di kursus tersebut (nama, progres, nilai, status sertifikat).

---

#### Tab / Seksi: Per Peserta

Tabel semua peserta dengan agregat capaian:

| Peserta | Kursus diikuti | Selesai | Sertifikat | Rata-rata nilai |
|---|---|---|---|---|
| Ahmad Fauzi | 4 | 3 | 3 | 85 |
| Siti Rahma | 2 | 1 | 1 | 78 |

Klik nama → halaman detail peserta (sama dengan detail peserta di manajemen learner).

---

#### Ekspor Laporan

Tombol **Unduh CSV** di setiap tab mengekspor data yang ditampilkan.  
Format: CSV utf-8, kolom sesuai tabel.  
Diproses sinkron (cukup untuk ratusan baris), pakai queued job kalau organisasi punya ribuan peserta.

---

## 6. Navigasi & URL

```
/org                          → redirect ke /org/reports
/org/reports                  → Dashboard capaian (ringkasan)
/org/reports/by-topic         → Capaian per topik
/org/reports/by-course        → Capaian per kursus
/org/reports/by-course/{uuid} → Drill-down peserta di kursus tertentu
/org/reports/by-learner       → Capaian per peserta

/org/learners                 → Daftar peserta
/org/learners/create          → Tambah peserta manual
/org/learners/import          → Form upload CSV
/org/learners/{uuid}          → Detail peserta
/org/learners/{uuid}/edit     → Edit peserta
```

Semua route di bawah middleware `auth` + gate `can('view org reports')` atau `can('manage org learners')`.

---

## 7. Rencana Implementasi

### Tahap 1 — Role & Permission

1. Tambah role `Org Admin` di `PermissionSeeder`
2. Tambah permission `manage org learners` dan `view org reports`
3. Assign ke `Org Admin` (dan `Admin` untuk `view org reports`)
4. Tambah link di sidebar admin untuk Org Admin

### Tahap 2 — Manajemen Peserta

1. `OrgLearnerController` (index, create, store, edit, update, destroy)
2. Views: index, create/edit form, show (detail peserta)
3. Tambah manual sudah hampir sama dengan `UserController` yang ada — tinggal scope ke org

### Tahap 3 — Import CSV

1. `OrgLearnerImportController` (show form, store)
2. `ImportLearnersJob` — queued, proses baris per baris
3. `LearnersImportResult` — simpan hasil (berhasil, gagal, alasan) di session atau tabel
4. Template CSV downloadable

### Tahap 4 — Dashboard Laporan

1. `OrgReportController` (index/ringkasan, byTopic, byCourse, byLearner)
2. `OrgReportService` — pisahkan query agregasi dari controller
3. Views dengan tabel + stat cards (pakai komponen yang sudah ada di project)
4. Filter periode (Litepicker) + filter kursus (Tom Select) — keduanya sudah ada

### Tahap 5 — Ekspor CSV

1. Tambah `export` action di `OrgReportController`
2. Response `StreamedResponse` dengan header CSV

---

## 8. Pertimbangan Teknis

### Scoping query

`BelongsToOrganization` membaca `organization_id()` dari session. Untuk fitur ini, pastikan Org Admin selalu punya `organization_id` yang benar di session saat login. Alternatif yang lebih eksplisit: gunakan `auth()->user()->organization_id` langsung di controller tanpa bergantung session.

### Performa query laporan

Untuk organisasi dengan ratusan peserta dan puluhan kursus, query agregasi di atas masih cepat. Kalau mulai lambat, tambahkan:
- Index pada `enrollments.user_id`, `enrollments.course_id`, `enrollments.completed_at`
- Cache hasil laporan per jam dengan `Cache::remember()`

### Keamanan

Setiap endpoint org wajib memvalidasi bahwa resource yang diakses (`user`, `enrollment`) memang milik organisasi admin yang login:

```php
abort_unless($targetUser->organization_id === auth()->user()->organization_id, 403);
```

Jangan pernah terima `organization_id` dari request — selalu ambil dari `auth()->user()->organization_id`.

---

## 9. Yang Tidak Termasuk (Out of Scope untuk sekarang)

- Satu peserta bergabung ke lebih dari satu organisasi (butuh aktivasi pivot `organization_user`)
- Org Admin membuat/mengedit kursus — itu tetap domain Admin/Instructor
- Notifikasi otomatis ke peserta saat belum menyelesaikan kursus
- Integrasi SSO/LDAP untuk import akun dari sistem HR organisasi mitra

---

## Appendix: Akun Demo & Data Studi Kasus

### Cara menjalankan seeder

```bash
# Fresh install (semua seeder termasuk MpzSeeder)
php artisan migrate:fresh --seed

# Hanya data MPZ tanpa reset database
php artisan db:seed --class=MpzSeeder
```

`MpzSeeder` otomatis dipanggil dari `DatabaseSeeder` sehingga sudah masuk ke flow fresh install.

---

### Akun demo yang tersedia

Semua akun menggunakan password: **`password`**

#### Platform operator

| Nama | Email | Role | Organisasi |
|---|---|---|---|
| Super Admin | `admin@example.com` | Super Admin | Yayasan Filantropi Islam |

#### MPZ Gerak Bareng — Admin Organisasi

| Nama | Email | Role | Jabatan |
|---|---|---|---|
| Admin MPZ | `admin@mpzgerakbareng.org` | Org Admin | Koordinator Pelatihan |

#### MPZ Gerak Bareng — Peserta (10 orang)

| Nama | Email | Jabatan | Kategori |
|---|---|---|---|
| Ahmad Fauzi | `ahmad.fauzi@mpzgerakbareng.org` | Staf Program | Officer |
| Siti Rahmawati | `siti.r@mpzgerakbareng.org` | Staf Keuangan | Officer |
| Budi Santoso | `budi.s@mpzgerakbareng.org` | Supervisor Program | Supervisor |
| Dewi Kusuma | `dewi.k@mpzgerakbareng.org` | Relawan Senior | Officer |
| Hendra Wijaya | `hendra.w@mpzgerakbareng.org` | Manajer Lapangan | Manajer |
| Fatimah Azzahra | `fatimah.a@mpzgerakbareng.org` | Staf Komunikasi | Officer |
| Rizky Pratama | `rizky.p@mpzgerakbareng.org` | Koordinator Relawan | Supervisor |
| Nurul Hidayah | `nurul.h@mpzgerakbareng.org` | Analis Data | Officer |
| Andi Kurniawan | `andi.k@mpzgerakbareng.org` | Staf IT | Officer |
| Maya Sari | `maya.s@mpzgerakbareng.org` | Direktur Program | Direktur |

---

### Data studi kasus yang di-seed

- **73 enrollment** tersebar di 15 kursus yang tersedia
- Setiap peserta mengikuti **4–8 kursus** secara acak
- **~60% enrollment selesai** (completed_at terisi), sisanya in-progress (20–85%)
- **171 quiz attempts** dengan skor 40–100 (selesai ≥70, in-progress bervariasi)
- **Sertifikat** diterbitkan untuk semua enrollment yang selesai
- Tanggal enrollment tersebar **30–180 hari ke belakang** dari tanggal seeder dijalankan

### Skenario demo laporan

| Skenario | URL |
|---|---|
| Laporan ringkasan MPZ | `/admin/org/reports?org_id=2` |
| Capaian per topik MPZ | `/admin/org/reports/by-topic?org_id=2` |
| Capaian per kursus MPZ | `/admin/org/reports/by-course?org_id=2` |
| Capaian per peserta MPZ | `/admin/org/reports/by-learner?org_id=2` |
| Login sebagai Org Admin MPZ | email: `admin@mpzgerakbareng.org` |
| Manajemen peserta MPZ | `/admin/org/learners` (setelah login sebagai Org Admin) |
