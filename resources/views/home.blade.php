<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Beranda | {{ config('app.name') }}</title>
<meta name="theme-color" content="#333333">
<meta name="description" content="Platform pelatihan dan sertifikasi online untuk lembaga filantropi Islam. Kursus mandiri, kuis, dan sertifikat yang bisa diverifikasi.">
<meta property="og:image" content="{{ asset('og-home.webp') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/css/tabler.min.css">
<style>@vite(['resources/css/app.css'])
/\* resource: resources/views/home-style.css \*/
</style>
</head>
<body>
<a class="skip-link" href="#konten">Lewati ke konten utama</a>

<!-- ============ HEADER ============ -->
<header class="site-header">
  <div class="container-xl">
    <div class="d-flex flex-wrap align-items-center justify-content-between py-3 gap-3">
      <a class="brand-mark" href="{{ route('home-id') }}" aria-label="{{ config('app.name') }}, ke beranda">
        <img class="brand-logo" src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" height="44" onerror="this.src='{{ asset('logo-fallback.svg') }}'">
      </a>
      <nav aria-label="Navigasi utama">
        <ul class="nav">
          <li class="nav-item"><a class="nav-link" href="{{ route('home-id') }}#kursus">Katalog</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('home-id') }}#tentang">Tentang</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('verifikasi') }}">Verifikasi sertifikat</a></li>
        </ul>
      </nav>
      <div class="d-flex align-items-center gap-2">
        <div class="dropdown">
          <button class="btn btn-ghost-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="visually-hidden">Bahasa: </span>Indonesia</button>
          <div class="dropdown-menu">
            <a class="dropdown-item active" href="{{ route('home-id') }}" hreflang="id">Indonesia</a>
            <a class="dropdown-item" href="{{ route('home-en') }}" hreflang="en">English</a>
          </div>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('login') }}">Masuk</a>
        <a class="btn btn-primary" href="{{ route('register') }}">Daftar gratis</a>
      </div>
    </div>
  </div>
</header>

<main id="konten">

  <!-- ============ HERO ============ -->
  <section class="hero" aria-labelledby="h-hero">
    <img src="{{ asset('hero-home.webp') }}" alt="" width="1600" height="700" fetchpriority="high" onerror="this.style.display='none'">
    <div class="container-xl">
      <h1 id="h-hero">Belajar filantropi Islam, kapan saja dan di mana saja</h1>
      <p class="lead mt-3 mb-4">Kursus mandiri untuk amil, relawan, dan mitra lembaga. Selesaikan kursus, dapatkan sertifikat yang bisa diverifikasi publik.</p>
      <form class="hero-search row g-2" action="{{ route('home-id') }}#kursus" method="get" role="search">
        <div class="col-12 col-sm">
          <label class="visually-hidden" for="q">Cari kursus</label>
          <input class="form-control" id="q" name="q" type="search" placeholder="Cari kursus, misalnya: dasar zakat">
        </div>
        <div class="col-12 col-sm-auto"><button class="btn btn-primary w-100" type="submit">Cari kursus</button></div>
      </form>
      <div class="meta" role="list">
        <span role="listitem"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="4" width="10" height="16" rx="2"/><path d="M11 5h2M12 17v.01"/></svg> Bisa diakses dari HP</span>
        <span role="listitem"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="15" cy="15" r="3"/><path d="M13 17.5V22l2-1.5l2 1.5v-4.5M10 19H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10M6 9h12M6 12h3"/></svg> Sertifikat digital</span>
        <span role="listitem"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3.6 9h16.8M3.6 15h16.8M11.5 3a17 17 0 0 0 0 18M12.5 3a17 17 0 0 1 0 18"/></svg> Indonesia &amp; English</span>
      </div>
    </div>
  </section>

  <!-- ============ TOPIK ============ -->
  <section class="section" aria-labelledby="h-topik">
    <div class="container-xl">
      <div class="section-head">
        <h2 class="h2" id="h-topik">Topik pelatihan</h2>
        <p>Pilih bidang yang sesuai dengan peran Anda di lembaga.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('home-id') }}?kategori=dasar-ziswaf"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19.5 12.572l-7.5 7.428l-7.5-7.428a5 5 0 1 1 7.5-6.566a5 5 0 1 1 7.5 6.572"/></svg></span><span class="txt">Dasar Ziswaf<small>Zakat, infak, sedekah, wakaf</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('home-id') }}?kategori=pengelolaan-zakat"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M14.8 9a2 2 0 0 0-1.8-1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1-1.8-1M12 6v2m0 8v2"/></svg></span><span class="txt">Pengelolaan Zakat<small>Himpun, salur, laporkan</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('home-id') }}?kategori=pemberdayaan"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.85"/></svg></span><span class="txt">Pemberdayaan Masyarakat<small>Program, UMKM, pendampingan</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('home-id') }}?kategori=tata-kelola"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1-8.5 15a12 12 0 0 1-8.5-15a12 12 0 0 0 8.5-3"/><path d="M9 12l2 2l4-4"/></svg></span><span class="txt">Tata Kelola &amp; Kepatuhan<small>Akuntabilitas, standar, pelaporan</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('home-id') }}?kategori=manajemen-lembaga"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/></svg></span><span class="txt">Manajemen Lembaga<small>Kepemimpinan, SDM, digitalisasi</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('home-id') }}?kategori=relawan-kebencanaan"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M15 15l3.35 3.35M9 15l-3.35 3.35M5.65 5.65L9 9m6-3.35L18.35 5.65M15 9l3.35-3.35"/></svg></span><span class="txt">Relawan &amp; Kebencanaan<small>Respon, keselamatan, safeguarding</small></span></a></div>
      </div>
    </div>
  </section>

  <!-- ============ TENTANG KAMI ============ -->
  <section class="section section-alt" aria-labelledby="h-tentang" id="tentang">
    <div class="container-xl">
      <div class="row g-5 align-items-center">
        <div class="col-lg-6">
          <h2 class="h2" id="h-tentang">Tentang kami</h2>
          <p class="lead-text">Akademi Filantropi adalah platform belajar online untuk amil, relawan, mitra, dan publik di lembaga filantropi Islam.</p>
          <a class="btn btn-primary" href="{{ route('home-id') }}#kursus">Lihat kursus</a>
        </div>
        <div class="col-lg-6">
          <div class="feature"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="4" width="10" height="16" rx="2"/><path d="M11 5h2M12 17v.01"/></svg></span><div><h3>Belajar mandiri</h3><p>Video, bacaan, dan kuis yang bisa diselesaikan dengan ritme Anda sendiri.</p></div></div>
          <div class="feature"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="15" cy="15" r="3"/><path d="M13 17.5V22l2-1.5l2 1.5v-4.5M10 19H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10M6 9h12M6 12h3"/></svg></span><div><h3>Sertifikat terverifikasi</h3><p>Setiap sertifikat punya kode dan QR untuk dicek siapa saja.</p></div></div>
          <div class="feature mb-0"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3.6 9h16.8M3.6 15h16.8M11.5 3a17 17 0 0 0 0 18M12.5 3a17 17 0 0 1 0 18"/></svg></span><div><h3>Dua bahasa</h3><p>Antarmuka dan sertifikat tersedia dalam bahasa Indonesia dan Inggris.</p></div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ ORGANISASI ANDA ============ -->
  <section class="section" aria-labelledby="h-org">
    <div class="container-xl">
      <div class="row g-4 align-items-center">
        <div class="col-md-6 order-md-2">
          <h2 class="h2" id="h-org">Untuk organisasi Anda</h2>
          <p class="lead-text">Selain kursus untuk individu, lembaga bisa menyusun, mengelola, dan membagikan pelatihannya sendiri lewat portal organisasi, lengkap dengan logo, warna, dan sertifikat atas nama lembaga.</p>
          <a class="btn btn-primary" href="{{ route('home-id') }}#kursus">Lihat solusi organisasi</a>
        </div>
        <div class="col-md-6 order-md-1">
          <img class="media-img" src="{{ asset('hero-org.webp') }}" alt="Seorang perempuan muda memimpin rapat perencanaan organisasi." width="720" height="480" loading="lazy" onerror="this.style.display='none'">
        </div>
      </div>
    </div>
  </section>

  <!-- ============ KURSUS TERSEDIA ============ -->
  <section class="section" aria-labelledby="h-kursus" id="kursus">
    <div class="container-xl">
      <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
        <div class="section-head mb-0">
          <h2 class="h2" id="h-kursus">Kursus tersedia</h2>
          <p>Kursus mandiri yang bisa langsung Anda ikuti.</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('home-id') }}?kategori=semua">Semua kursus</a>
      </div>
      <div class="row g-4">
        <article class="col-md-6 col-lg-4">
          <div class="card course-card h-100">
            <img class="course-img" src="{{ asset('course-1.webp') }}" alt="Dua orang belajar dari laptop" style="--pos:20% 40%" width="640" height="360" loading="lazy" onerror="this.style.display='none'">
            <div class="card-body">
              <span class="badge bg-green-lt badge-access mb-2">Gratis</span>
              <h3 class="h3"><a class="text-reset text-decoration-none stretched-link" href="{{ route('home-id') }}/kursus/pengantar-zakat">Pengantar Zakat untuk Relawan</a></h3>
              <p class="text-secondary">Dasar zakat dan peran relawan dalam penghimpunan.</p>
              <div class="course-meta"><span>Pemula</span><span>2 jam</span></div>
            </div>
            <div class="card-footer text-secondary">Sertifikat gratis</div>
          </div>
        </article>
        <article class="col-md-6 col-lg-4">
          <div class="card course-card h-100">
            <img class="course-img" src="{{ asset('course-2.webp') }}" alt="Perempuan memimpin rapat perencanaan" style="--pos:50% 30%" width="640" height="360" loading="lazy" onerror="this.style.display='none'">
            <div class="card-body">
              <span class="badge bg-azure-lt badge-access mb-2">Donasi untuk masuk</span>
              <h3 class="h3"><a class="text-reset text-decoration-none stretched-link" href="{{ route('home-id') }}/kursus/dasar-pelaporan-program">Dasar Pelaporan Program</a></h3>
              <p class="text-secondary">Menyusun laporan program yang akuntabel.</p>
              <div class="course-meta"><span>Menengah</span><span>4 jam</span></div>
            </div>
            <div class="card-footer text-secondary">Sertifikat gratis</div>
          </div>
        </article>
        <article class="col-md-6 col-lg-4">
          <div class="card course-card h-100">
            <img class="course-img" src="{{ asset('course-3.webp') }}" alt="Peserta pelatihan di lingkungan desa" style="--pos:85% 60%" width="640" height="360" loading="lazy" onerror="this.style.display='none'">
            <div class="card-body">
              <span class="badge bg-green-lt badge-access mb-2">Gratis</span>
              <h3 class="h3"><a class="text-reset text-decoration-none stretched-link" href="{{ route('home-id') }}/kursus/dasar-fasilitasi-tot">Dasar Fasilitasi (ToT)</a></h3>
              <p class="text-secondary">Prasyarat untuk mengelola pelatihan sebagai penyelenggara.</p>
              <div class="course-meta"><span>Menengah</span><span>6 jam</span></div>
            </div>
            <div class="card-footer text-secondary">Sertifikat via donasi</div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- ============ VERIFIKASI SERTIFIKAT ============ -->
  <section class="verify" aria-labelledby="h-verif">
    <div class="container-xl">
      <div class="row g-3 align-items-center">
        <div class="col-lg-5">
          <h2 class="h2 text-white mb-1" id="h-verif">Verifikasi sertifikat</h2>
          <p class="mb-0">Masukkan kode pada sertifikat untuk memeriksa keasliannya.</p>
        </div>
        <div class="col-lg-7">
          <form class="row g-2" action="{{ route('verifikasi') }}" method="get">
            <div class="col">
              <label class="visually-hidden" for="kode">Kode sertifikat</label>
              <input class="form-control" id="kode" name="kode" placeholder="Contoh: AF-2026-000123">
            </div>
            <div class="col-auto"><button class="btn btn-primary h-100" type="submit">Periksa kode</button></div>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- ============ FOOTER ============ -->
<footer class="site-footer">
  <div class="container-xl">
    <div class="row g-4">
      <div class="col-md-4">
        <a href="{{ route('home-id') }}"><img class="footer-logo" src="{{ asset('logo-en.png') }}" alt="{{ config('app.name') }}" height="44" onerror="this.src='{{ asset('logo-fallback.svg') }}'"></a>
        <p class="mt-3">Pelatihan dan sertifikasi online untuk filantropi Islam.</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="footer-title">Tentang</h2>
        <ul>
          <li><a href="{{ route('home-id') }}#tentang">Apa itu Akademi Filantropi?</a></li>
          <li><a href="{{ route('home-id') }}#org">Solusi organisasi</a></li>
          <li><a href="{{ route('home-id') }}?syarat">Syarat dan ketentuan</a></li>
          <li><a href="{{ route('home-id') }}?privasi">Kebijakan privasi</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="footer-title">Bantuan</h2>
        <ul>
          <li><a href="{{ route('home-id') }}?bantuan">Butuh bantuan?</a></li>
          <li><a href="{{ route('home-id') }}?panduan">Panduan memulai</a></li>
          <li><a href="{{ route('home-en') }}">English</a></li>
        </ul>
      </div>
      <div class="col-md-2">
        <h2 class="footer-title">Ikuti kami</h2>
        <ul>
          <li><a href="#" rel="noopener">Instagram</a></li>
          <li><a href="#" rel="noopener">YouTube</a></li>
          <li><a href="#" rel="noopener">LinkedIn</a></li>
        </ul>
      </div>
    </div>
    <div class="legal">&copy; <?php echo date('Y'); ?> {{ config('app.name') }}. Seluruh hak dilindungi.</div>
  </div>
</footer>

@vite('resources/js/app.js')
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/js/tabler.min.js"></script>
</body>
</html>
