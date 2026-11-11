<x-layouts.public
    title="Beranda"
    description="Platform belajar online untuk orang muda yang ingin memahami dan mempraktikkan filantropi Islam. Kursus mandiri, sertifikat digital yang bisa diverifikasi."
    :og-image="asset('images/hero-home.webp')"
>

  <!-- ============ HERO ============ -->
  <section class="hero" aria-labelledby="h-hero">
    <img src="{{ asset('images/hero-home.webp') }}" alt="" width="1600" height="700" fetchpriority="high" onerror="this.style.display='none'">
    <div class="container-xl">
      <h1 id="h-hero">Belajar filantropi Islam, kapan saja dan di mana saja</h1>
      <p class="lead mt-3 mb-4">Kursus mandiri untuk orang muda, amil, relawan, dan mitra lembaga. Selesaikan kursus, dapatkan sertifikat yang bisa diverifikasi publik.</p>
      <form class="hero-search row g-2" action="{{ route('catalog.index') }}" method="get" role="search">
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
        <p>Pilih bidang yang sesuai dengan peran kamu di lembaga.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('catalog.index', ['kategori' => 'dasar-ziswaf']) }}"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19.5 12.572l-7.5 7.428l-7.5-7.428a5 5 0 1 1 7.5-6.566a5 5 0 1 1 7.5 6.572"/></svg></span><span class="txt">Dasar Ziswaf<small>Zakat, infak, sedekah, wakaf</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('catalog.index', ['kategori' => 'pengelolaan-zakat']) }}"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M14.8 9a2 2 0 0 0-1.8-1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1-1.8-1M12 6v2m0 8v2"/></svg></span><span class="txt">Pengelolaan Zakat<small>Himpun, salur, laporkan</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('catalog.index', ['kategori' => 'pemberdayaan']) }}"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.85"/></svg></span><span class="txt">Pemberdayaan Masyarakat<small>Program, UMKM, pendampingan</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('catalog.index', ['kategori' => 'tata-kelola']) }}"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1-8.5 15a12 12 0 0 1-8.5-15a12 12 0 0 0 8.5-3"/><path d="M9 12l2 2l4-4"/></svg></span><span class="txt">Tata Kelola &amp; Kepatuhan<small>Akuntabilitas, standar, pelaporan</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('catalog.index', ['kategori' => 'manajemen-lembaga']) }}"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/></svg></span><span class="txt">Manajemen Lembaga<small>Kepemimpinan, SDM, digitalisasi</small></span></a></div>
        <div class="col-md-6 col-lg-4"><a class="topic" href="{{ route('catalog.index', ['kategori' => 'relawan-kebencanaan']) }}"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M15 15l3.35 3.35M9 15l-3.35 3.35M5.65 5.65L9 9m6-3.35L18.35 5.65M15 9l3.35-3.35"/></svg></span><span class="txt">Relawan &amp; Kebencanaan<small>Respon, keselamatan, safeguarding</small></span></a></div>
      </div>
    </div>
  </section>

  <!-- ============ TENTANG ============ -->
  <section class="section section-alt" aria-labelledby="h-tentang" id="tentang">
    <div class="container-xl">
      <div class="row g-5 align-items-center">
        <div class="col-lg-6">
          <h2 class="h2" id="h-tentang">Tentang NextUs</h2>
          <p class="lead-text">NextUs adalah platform belajar online untuk orang muda yang ingin memahami dan mempraktikkan filantropi Islam. Ilmu baik, diteruskan.</p>
          <a class="btn btn-primary" href="{{ route('tentang') }}">Pelajari lebih lanjut</a>
        </div>
        <div class="col-lg-6">
          <div class="feature"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="4" width="10" height="16" rx="2"/><path d="M11 5h2M12 17v.01"/></svg></span><div><h3>Belajar mandiri</h3><p>Video, bacaan, dan kuis yang bisa diselesaikan dengan ritme kamu sendiri.</p></div></div>
          <div class="feature"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="15" cy="15" r="3"/><path d="M13 17.5V22l2-1.5l2 1.5v-4.5M10 19H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10M6 9h12M6 12h3"/></svg></span><div><h3>Sertifikat terverifikasi</h3><p>Setiap sertifikat punya kode dan QR untuk dicek siapa saja.</p></div></div>
          <div class="feature mb-0"><span class="ico"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3.6 9h16.8M3.6 15h16.8M11.5 3a17 17 0 0 0 0 18M12.5 3a17 17 0 0 1 0 18"/></svg></span><div><h3>Dua bahasa</h3><p>Antarmuka dan sertifikat tersedia dalam bahasa Indonesia dan Inggris.</p></div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ ORGANISASI ============ -->
  <section class="section" aria-labelledby="h-org" id="org">
    <div class="container-xl">
      <div class="row g-4 align-items-center">
        <div class="col-md-6 order-md-2">
          <h2 class="h2" id="h-org">Untuk organisasi kamu</h2>
          <p class="lead-text">Lembaga bisa menyusun, mengelola, dan membagikan pelatihannya sendiri lewat portal organisasi, lengkap dengan logo, warna, dan sertifikat atas nama lembaga.</p>
          <a class="btn btn-primary" href="{{ route('tentang') }}#organisasi">Lihat solusi organisasi</a>
        </div>
        <div class="col-md-6 order-md-1">
          <img class="media-img" src="{{ asset('images/hero-org.webp') }}" alt="Seorang perempuan muda memimpin rapat perencanaan organisasi." width="720" height="480" loading="lazy" onerror="this.style.display='none'">
        </div>
      </div>
    </div>
  </section>

  <!-- ============ KELAS PILIHAN EDITOR ============ -->
  @if (isset($featuredCourses) && $featuredCourses->isNotEmpty())
  <section class="section" aria-labelledby="h-featured">
    <div class="container-xl">
      <div class="section-head">
        <h2 class="h2" id="h-featured">Kelas Pilihan Editor</h2>
        <p>Kursus pilihan yang direkomendasikan untuk kamu.</p>
      </div>
      <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          @foreach ($featuredCourses->chunk(4) as $index => $chunk)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
              <div class="row g-4">
                @foreach ($chunk as $course)
                  <div class="col-md-6 col-lg-3">
                    <article class="h-100">
                      <div class="card course-card h-100">
                        @if ($course->thumbnail)
                          <img class="course-img" src="{{ route('media.thumbnail', ['path' => $course->thumbnail]) }}" alt="{{ $course->getTranslation('title', app()->getLocale()) }}" width="640" height="360" loading="lazy">
                        @else
                          <div class="course-img" style="background:var(--nu-zamrud-100);display:flex;align-items:center;justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--nu-zamrud-400)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                          </div>
                        @endif
                        <div class="card-body">
                          <h3 class="h3">
                            <a class="text-reset text-decoration-none stretched-link" href="{{ route('catalog.show', $course->slug) }}">
                              {{ $course->getTranslation('title', app()->getLocale()) }}
                            </a>
                          </h3>
                          <p class="text-secondary">{{ Str::limit($course->getTranslation('summary', app()->getLocale()), 80) }}</p>
                        </div>
                      </div>
                    </article>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
        @if ($featuredCourses->count() > 4)
          <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sebelumnya</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Berikutnya</span>
          </button>
        @endif
      </div>
    </div>
  </section>
  @endif

  <!-- ============ KURSUS TERSEDIA ============ -->
  <section class="section" aria-labelledby="h-kursus" id="kursus">
    <div class="container-xl">
      <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
        <div class="section-head mb-0">
          <h2 class="h2" id="h-kursus">Kursus Terbaru</h2>
          <p>Kursus mandiri yang bisa langsung kamu ikuti.</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('catalog.index') }}">Semua kursus</a>
      </div>
      <div class="row g-4">
        @php
          $levelLabels = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'];
          $accessLabels = ['free' => 'Gratis', 'donation' => 'Donasi untuk masuk', 'paid' => 'Berbayar'];
        @endphp
        @forelse ($courses as $course)
          <article class="col-md-6 col-lg-3">
            <div class="card course-card h-100">
              @if ($course->thumbnail)
                <img class="course-img" src="{{ route('media.thumbnail', ['path' => $course->thumbnail]) }}" alt="{{ $course->getTranslation('title', app()->getLocale()) }}" width="640" height="360" loading="lazy">
              @else
                <div class="course-img" style="background:var(--nu-zamrud-100);display:flex;align-items:center;justify-content:center;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--nu-zamrud-400)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                </div>
              @endif
              <div class="card-body">
                <span class="badge {{ $course->access_type === 'free' ? 'bg-green-lt' : ($course->access_type === 'donation' ? 'bg-azure-lt' : 'bg-orange-lt') }} badge-access mb-2">
                  {{ $accessLabels[$course->access_type] ?? $course->access_type }}
                </span>
                <h3 class="h3">
                  <a class="text-reset text-decoration-none stretched-link" href="{{ route('catalog.show', $course->slug) }}">
                    {{ $course->getTranslation('title', app()->getLocale()) }}
                  </a>
                </h3>
                <p class="text-secondary">{{ Str::limit($course->getTranslation('summary', app()->getLocale()), 100) }}</p>
                <div class="course-meta">
                  @if ($course->level)
                    <span>{{ $levelLabels[$course->level] ?? $course->level }}</span>
                  @endif
                  @if ($course->duration_minutes)
                    <span>{{ round($course->duration_minutes / 60) }} jam</span>
                  @endif
                </div>
              </div>
              <div class="card-footer">
                {{ $course->certificate_type === 'free' ? 'Sertifikat gratis' : ($course->certificate_type === 'donation' ? 'Sertifikat via donasi' : 'Sertifikat berbayar') }}
              </div>
            </div>
          </article>
        @empty
          <div class="col-12">
            <div class="alert alert-secondary mb-0">Belum ada kursus yang dipublikasikan.</div>
          </div>
        @endforelse
      </div>

      @if ($courses->count() === 6)
        <div class="text-center mt-4">
          <a class="btn btn-outline-primary" href="{{ route('catalog.index') }}">Selengkapnya</a>
        </div>
      @endif
    </div>
  </section>

  <!-- ============ MITRA & SPONSOR ============ -->
  @if ($sponsors->isNotEmpty())
    <section class="section section-alt">
      <div class="container-xl">
        <h2 class="text-center mb-4">Mitra &amp; Sponsor Kami</h2>
        <div class="sponsor-slider d-flex flex-wrap justify-content-center gap-4 align-items-center">
          @foreach ($sponsors as $sponsor)
            @if ($sponsor->url)
              <a href="{{ $sponsor->url }}" target="_blank" rel="noopener"
                 class="d-block text-decoration-none">
                <img src="{{ Storage::disk('public')->url($sponsor->logo) }}"
                     alt="{{ $sponsor->name }}"
                     class="sponsor-logo img-fluid"
                     style="max-height: 50px; max-width: 180px; object-fit: contain; opacity: 0.85; transition: opacity 0.2s;"
                     onmouseover="this.style.opacity='1'"
                     onmouseout="this.style.opacity='0.85'">
              </a>
            @else
              <img src="{{ Storage::disk('public')->url($sponsor->logo) }}"
                   alt="{{ $sponsor->name }}"
                   class="sponsor-logo img-fluid"
                   style="max-height: 50px; max-width: 180px; object-fit: contain; opacity: 0.85; transition: opacity 0.2s;"
                   onmouseover="this.style.opacity='1'"
                   onmouseout="this.style.opacity='0.85'">
            @endif
          @endforeach
        </div>
      </div>
    </section>
  @endif

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
              <input class="form-control" id="kode" name="kode" placeholder="Contoh: NU-2026-000123">
            </div>
            <div class="col-auto"><button class="btn btn-primary h-100" type="submit">Periksa kode</button></div>
          </form>
        </div>
      </div>
    </div>
  </section>

</x-layouts.public>

