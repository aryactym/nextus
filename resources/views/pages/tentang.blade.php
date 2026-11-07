<x-layouts.public
    title="Tentang NextUs"
    description="Platform belajar online filantropi Islam untuk amil, relawan, mitra, dan publik."
    active-page="about"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li aria-current="page">Tentang</li>
      </ol>
      <h1>Tentang NextUs</h1>
      <p>Platform belajar online untuk amil, relawan, mitra, dan publik di lembaga filantropi Islam.</p>
    </div>
  </section>

  <section class="section" aria-labelledby="h-apa">
    <div class="container-xl">
      <div class="row g-5 align-items-center">
        <div class="col-lg-6">
          <h2 class="h2" id="h-apa">Belajar yang mudah diakses, hasil yang bisa dipercaya</h2>
          <p class="lead-text">NextUs menyediakan kursus mandiri berisi video, bacaan, dan kuis. Setiap peserta yang menyelesaikan kursus mendapat sertifikat digital yang dapat diverifikasi oleh siapa saja.</p>
          <p>Antarmuka dan sertifikat tersedia dalam bahasa Indonesia dan Inggris, serta nyaman dibuka dari HP.</p>
        </div>
        <div class="col-lg-6">
          <img class="media-img" src="{{ asset('images/hero-org.webp') }}" alt="Perempuan muda memimpin rapat perencanaan organisasi" width="720" height="480" loading="lazy" onerror="this.style.display='none'">
        </div>
      </div>
    </div>
  </section>

  <section class="section" style="background:var(--nu-kertas)" aria-labelledby="h-siapa">
    <div class="container-xl">
      <div class="section-head">
        <h2 class="h2" id="h-siapa">Untuk siapa</h2>
        <p>Kursus disusun untuk berbagai peran di ekosistem filantropi Islam.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h3">Amil</h3>
              <p>Memperdalam pengelolaan zakat, dari penghimpunan hingga pelaporan.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h3">Relawan</h3>
              <p>Membekali diri sebelum turun ke lapangan dan program.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h3">Mitra lembaga</h3>
              <p>Menyamakan pemahaman dan standar kerja bersama.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h3">Publik</h3>
              <p>Mengenal dasar zakat, infak, sedekah, dan wakaf.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" aria-labelledby="h-langkah">
    <div class="container-xl">
      <div class="section-head">
        <h2 class="h2" id="h-langkah">Cara belajar</h2>
        <p>Tiga langkah dari mendaftar sampai memegang sertifikat.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h3"><span class="badge bg-azure-lt">1</span> Daftar gratis</h3>
              <p>Buat akun dalam beberapa menit, lalu pilih kursus dari katalog.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h3"><span class="badge bg-azure-lt">2</span> Belajar mandiri</h3>
              <p>Ikuti video, bacaan, dan kuis dengan ritme kamu sendiri.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h3"><span class="badge bg-azure-lt">3</span> Dapatkan sertifikat</h3>
              <p>Sertifikat punya kode dan QR yang bisa dicek di halaman verifikasi.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container-xl">
      <h2>Untuk organisasi kamu</h2>
      <p>Lembaga bisa menyusun, mengelola, dan membagikan pelatihannya sendiri lewat portal organisasi, lengkap dengan logo, warna, dan sertifikat atas nama lembaga.</p>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-primary" href="{{ route('catalog.index') }}">Mulai belajar</a>
        <a class="btn btn-outline-primary" href="{{ route('panduan') }}">Panduan memulai</a>
      </div>
    </div>
  </section>

</x-layouts.public>

