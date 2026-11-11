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
        @php
          $levelLabels = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'];
          $accessLabels = ['free' => 'Gratis', 'donation' => 'Donasi untuk masuk', 'paid' => 'Berbayar'];
        @endphp
        @forelse ($courses as $course)
          <article class="col-md-6 col-lg-4">
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

