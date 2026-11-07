<x-layouts.public
    title="Belajar saya"
    description="Daftar kursus yang sedang kamu ikuti beserta progresnya."
    active-page="catalog"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li aria-current="page">Belajar saya</li>
      </ol>
      <h1>Belajar saya</h1>
      <p>Lanjutkan kursus yang sedang kamu ikuti.</p>
    </div>
  </section>

  <section class="section pt-0">
    <div class="container-xl">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-2">
            <p class="mb-0 fw-bold">
              @if ($level['next_threshold'] !== null)
                Level {{ $level['level'] }} {{ $level['name_id'] }}, {{ $totalXp }}/{{ $level['next_threshold'] }} XP
              @else
                Level {{ $level['level'] }} {{ $level['name_id'] }}, {{ $totalXp }} XP
              @endif
            </p>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('learning.achievements') }}">Lihat pencapaian</a>
          </div>
          <div class="progress progress-bar-nu"><div class="progress-bar" style="width: {{ $levelProgressPercent }}%"></div></div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      @if ($enrollments->isEmpty())
        <div class="empty">
          <h2>Belum ada kursus yang diikuti</h2>
          <p class="text-secondary">Jelajahi katalog dan mulai belajar hari ini.</p>
          <a class="btn btn-primary" href="{{ route('catalog.index') }}">Lihat katalog</a>
        </div>
      @else
        <div class="row g-4">
          @foreach ($enrollments as $enrollment)
            @php
              $course = $enrollment->course;
              $nextLesson = $nextLessonByEnrollment[$enrollment->id] ?? null;
              $completedCount = $completedByEnrollment[$enrollment->id]->count();
              $totalCount = $course->modules->flatMap->lessons->count();
            @endphp
            <article class="col-md-6 col-xl-4">
              <div class="card course-card h-100">
                @if ($course->thumbnail)
                  <img
                    class="course-img"
                    src="{{ route('media.thumbnail', ['path' => $course->thumbnail]) }}"
                    alt="{{ $course->getTranslation('title', app()->getLocale()) }}"
                    width="640" height="360" loading="lazy"
                  >
                @endif
                <div class="card-body">
                  <span class="topic-label">{{ $course->category?->getTranslation('name', app()->getLocale()) }}</span>
                  <h3 class="h3">{{ $course->getTranslation('title', app()->getLocale()) }}</h3>
                  <p class="text-secondary">
                    @if ($enrollment->status === 'completed')
                      Selesai
                    @else
                      {{ $completedCount }} dari {{ $totalCount }} materi
                    @endif
                  </p>
                  <div class="progress progress-bar-nu"><div class="progress-bar" style="width: {{ $enrollment->progress_percent }}%"></div></div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                  <span>{{ $enrollment->progress_percent }}%</span>
                  @if ($enrollment->status === 'completed')
                    @if ($enrollment->certificate)
                      <a class="btn btn-primary btn-sm" href="{{ route('certificates.show', $enrollment->certificate->code) }}">Lihat sertifikat</a>
                    @elseif ($enrollment->course->certificate_type !== 'free')
                      <a class="btn btn-outline-primary btn-sm" href="{{ route('catalog.show', $course->slug) }}">Buka sertifikat</a>
                    @else
                      <span class="badge bg-green-lt">Selesai</span>
                    @endif
                  @elseif ($nextLesson)
                    <a class="btn btn-primary btn-sm" href="{{ route('player.show', ['course' => $course->slug, 'lesson' => $nextLesson->uuid]) }}">Lanjutkan</a>
                  @else
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('catalog.show', $course->slug) }}">Lihat kursus</a>
                  @endif
                </div>
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  @if ($certificates->isNotEmpty())
    <section class="section pt-0">
      <div class="container-xl">
        <h2 class="h2 mb-3">Sertifikat saya</h2>
        <div class="row g-3">
          @foreach ($certificates as $certificate)
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-start gap-3">
                  <div>
                    <span class="topic-label">Sertifikat penyelesaian</span>
                    <h3 class="h3 mb-1">{{ $certificate->course->getTranslation('title', app()->getLocale()) }}</h3>
                    <p class="text-secondary mb-0">
                      Kode {{ $certificate->code }} &middot;
                      {{ $certificate->issued_at->translatedFormat('d F Y') }}
                    </p>
                  </div>
                  <a class="btn btn-primary btn-sm" href="{{ route('certificates.show', $certificate->code) }}">Lihat</a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

</x-layouts.public>

