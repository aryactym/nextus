<x-layouts.public
    title="Halaman tidak ditemukan"
    description="Halaman yang Anda cari tidak tersedia di {{ config('app.name') }}."
    active-page="catalog"
>

  @php
    $courses = \App\Domain\Catalog\Models\Course::withoutGlobalScope('organization')
        ->where('status', 'published')
        ->with('category')
        ->latest()
        ->limit(8)
        ->get();

    $categories = \App\Domain\Catalog\Models\Category::withoutGlobalScope('organization')
        ->withCount(['courses' => fn ($q) => $q->where('status', 'published')])
        ->orderBy('sort')
        ->limit(6)
        ->get();
  @endphp

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li aria-current="page">Halaman tidak ditemukan</li>
      </ol>
      <h1>Maaf, halaman tidak ditemukan</h1>
      <p>Halaman yang Anda akses tidak tersedia atau telah dipindahkan. Coba cari kursus di bawah ini atau kembali ke beranda.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row g-4">

        {{-- ===== PENCARIAN KURSUS ===== --}}
        <div class="col-lg-4 mb-4">
          <div class="card">
            <div class="card-body">
              <h3 class="card-title mb-3">Cari kursus</h3>
              <form method="GET" action="{{ route('catalog.index') }}" class="d-flex gap-2">
                <input
                  type="search"
                  name="q"
                  class="form-control"
                  placeholder="Misalnya: zakat, shalat, infak"
                  aria-label="Cari kursus"
                >
                <button type="submit" class="btn btn-primary">Cari</button>
              </form>
              <div class="mt-3">
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary w-100">Lihat semua kursus</a>
              </div>
            </div>
          </div>
        </div>

        {{-- ===== KARTU KURSUS ===== --}}
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Kursus populer</h3>
            </div>
            <div class="card-body">
              @if ($courses->isEmpty())
                <div class="empty text-center py-4">
                  <h2>Belum ada kursus tersedia</h2>
                  <p class="text-secondary">Coba kembali nanti atau hubungi administrator.</p>
                </div>
              @else
                <div class="row g-4">
                  @foreach ($courses as $course)
                    <article class="col-md-6 col-xl-4">
                      <div class="card course-card h-100">
                        @if ($course->thumbnail)
                          <img
                            class="course-img"
                            src="{{ route('media.thumbnail', ['path' => $course->thumbnail]) }}"
                            alt="{{ $course->getTranslation('title', app()->getLocale()) }}"
                            width="640" height="360" loading="lazy"
                          >
                        @else
                          <div class="course-img" style="background:var(--nu-zamrud-100);display:flex;align-items:center;justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--nu-zamrud-400)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                          </div>
                        @endif
                        <div class="card-body">
                          @if ($course->category)
                            <span class="topic-label">{{ $course->category->getTranslation('name', app()->getLocale()) }}</span>
                          @endif
                          <span class="badge {{ $course->access_type === 'free' ? 'bg-green-lt' : ($course->access_type === 'donation' ? 'bg-azure-lt' : 'bg-orange-lt') }} badge-access mb-2">
                            {{ $course->access_type === 'free' ? 'Gratis' : ($course->access_type === 'donation' ? 'Donasi untuk masuk' : 'Berbayar') }}
                          </span>
                          <h3 class="h3">
                            <a class="text-reset text-decoration-none stretched-link" href="{{ route('catalog.show', $course->slug) }}">
                              {{ $course->getTranslation('title', app()->getLocale()) }}
                            </a>
                          </h3>
                          @if ($course->summary)
                            <p class="text-secondary">{{ \Illuminate\Support\Str::limit($course->getTranslation('summary', app()->getLocale()), 100) }}</p>
                          @endif
                          <div class="course-meta">
                            @if ($course->level)
                              <span>{{ ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'][$course->level] ?? $course->level }}</span>
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
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

</x-layouts.public>
