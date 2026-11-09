<x-layouts.public
    title="{{ $course->getTranslation('title', app()->getLocale()) }}"
    description="{{ $course->getTranslation('summary', app()->getLocale()) }}"
    active-page="catalog"
    canonical="{{ route('catalog.show', $course->slug) }}"
>

@php
    $locale = app()->getLocale();
    $title = $course->getTranslation('title', $locale);
    $summary = $course->getTranslation('summary', $locale);
    $description = $course->getTranslation('description', $locale);

    $levelLabels = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'];
    $accessLabels = ['free' => 'Gratis', 'donation' => 'Donasi untuk masuk', 'paid' => 'Berbayar'];
    $certificateLabels = ['free' => 'Sertifikat gratis', 'donation' => 'Sertifikat via donasi', 'paid' => 'Sertifikat berbayar'];

    $modules = $course->modules;
    $lessons = $modules->flatMap(fn ($module) => $module->lessons);
    $lessonCount = $lessons->count();
    $durationHours = $course->duration_minutes ? round($course->duration_minutes / 60, 1) : null;
    $durationLabel = $durationHours !== null
        ? rtrim(rtrim(number_format($durationHours, 1, ',', '.'), '0'), ',').' jam'
        : null;

    $learningOutcomes = $lessons
        ->filter(fn ($lesson) => $lesson->type !== 'quiz')
        ->take(6)
        ->map(fn ($lesson) => $lesson->getTranslation('title', $locale))
        ->values();

    if ($learningOutcomes->isEmpty() && $summary) {
        $learningOutcomes = collect([$summary]);
    }

    $organization = $course->organization;
    $organizationName = $organization?->getTranslation('name', $locale) ?? config('app.name');

    $avatar = "data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect width='96' height='96' fill='%23D4E4E2'/%3E%3Ccircle cx='48' cy='36' r='16' fill='%230E6B5C'/%3E%3Cpath d='M16 96c0-18 14-30 32-30s32 12 32 30z' fill='%230E6B5C'/%3E%3C/svg%3E";

    $formatRupiah = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
    $priceLabel = match ($course->access_type) {
        'free' => 'Gratis',
        'donation' => (float) $course->min_amount > 0 ? 'Donasi mulai '.$formatRupiah($course->min_amount) : 'Donasi',
        default => (float) $course->min_amount > 0 ? $formatRupiah($course->min_amount) : 'Berbayar',
    };
    $certificateNote = match ($course->access_type) {
        'free' => 'Sertifikat gratis setelah menyelesaikan kursus.',
        'donation' => 'Sertifikat tersedia setelah donasi dan menyelesaikan kursus.',
        default => 'Sertifikat tersedia setelah pembayaran dan menyelesaikan kursus.',
    };
@endphp

  <!-- ============ HEADER KURSUS ============ -->
  <section class="course-hero" aria-labelledby="h-kursus">
    <div class="container-xl">
      <nav class="crumb" aria-label="Breadcrumb">
        <ol>
          <li><a href="{{ route('home') }}">Beranda</a></li>
          <li><a href="{{ route('catalog.index') }}">Katalog</a></li>
          <li aria-current="page">{{ $title }}</li>
        </ol>
      </nav>
      <div class="row">
        <div class="col-lg-8">
          <span class="badge {{ $course->access_type === 'free' ? 'bg-green-lt' : ($course->access_type === 'donation' ? 'bg-azure-lt' : 'bg-orange-lt') }} badge-access">
            {{ $accessLabels[$course->access_type] ?? $course->access_type }}
          </span>
          <h1 id="h-kursus">{{ $title }}</h1>
          @if ($summary)
            <p class="lead-text">{{ $summary }}</p>
          @endif
          <ul class="chips">
            @if ($course->category)
              <li>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h6l10 10l-6 6L4 10z"/><circle cx="7.5" cy="7.5" r="1"/></svg>
                {{ $course->category->getTranslation('name', $locale) }}
              </li>
            @endif
            <li>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4V12H4zM10 20h4V8h-4zM16 20h4V4h-4z"/></svg>
              {{ $levelLabels[$course->level] ?? $course->level }}
            </li>
            @if ($durationLabel)
              <li>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                {{ $durationLabel }}
              </li>
            @endif
            <li>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0M3 6v13M12 6v13M21 6v13"/></svg>
              {{ $modules->count() }} modul
            </li>
            <li>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="4" width="10" height="16" rx="2"/><path d="M11 5h2M12 17v.01"/></svg>
              Belajar mandiri
            </li>
            <li>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="15" cy="15" r="3"/><path d="M13 17.5V22l2-1.5l2 1.5v-4.5M10 19H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10M6 9h12M6 12h3"/></svg>
              {{ $certificateLabels[$course->certificate_type] ?? $certificateLabels['free'] }}
            </li>
          </ul>
          <div class="hero-authors">
            <span class="stack"><img src="{{ $avatar }}" alt="" width="36" height="36"></span>
            <span>Disusun oleh <strong>{{ $organizationName }}</strong></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ ISI ============ -->
  <section class="section" style="padding-top:clamp(2rem,4vw,3rem)">
    <div class="container-xl">
      <div class="row g-5">
        <div class="col-lg-8 order-2 order-lg-1">

          @if (session('enroll_success'))
            <div class="form-alert show mb-3" role="status">{{ session('enroll_success') }}</div>
          @endif
          @if (session('enroll_error'))
            <div class="form-alert show mb-3" role="alert">{{ session('enroll_error') }}</div>
          @endif

          <div class="block" id="tentang">
            <h2>Tentang kursus</h2>
            @if ($description)
              <div class="lead-text">{!! Str::markdown($description) !!}</div>
            @elseif ($summary)
              <p>{{ $summary }}</p>
            @endif
          </div>

          @if ($learningOutcomes->isNotEmpty())
            <div class="block">
              <h2>Yang akan Anda pelajari</h2>
              <ul class="learn">
                @foreach ($learningOutcomes as $outcome)
                  <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2l4-4"/></svg>
                    <span>{{ $outcome }}</span>
                  </li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="block" id="kurikulum-blok">
            <h2>Kurikulum</h2>
            <p class="mb-3">
              {{ $modules->count() }} modul &middot; {{ $lessonCount }} materi
              @if ($durationLabel)
                &middot; total sekitar {{ $durationLabel }}
              @endif
            </p>

            @if ($modules->isEmpty())
              <p class="text-secondary">Materi sedang disiapkan. Silakan kembali lagi.</p>
            @else
              <div class="accordion" id="kurikulum">
                @foreach ($modules as $module)
                  <div class="accordion-item">
                    <h3 class="accordion-header">
                      <button
                        class="accordion-button{{ ! $loop->first ? ' collapsed' : '' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#module-{{ $module->id }}"
                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="module-{{ $module->id }}"
                      >
                        <span class="mod-no">{{ $loop->iteration }}</span>
                        <span>{{ $module->getTranslation('title', $locale) }}</span>
                        <span class="mod-time">{{ $module->lessons->sum('duration') }} mnt</span>
                      </button>
                    </h3>
                    <div id="module-{{ $module->id }}" class="accordion-collapse collapse{{ $loop->first ? ' show' : '' }}" data-bs-parent="#kurikulum">
                      <div class="accordion-body">
                        <ul class="lessons">
                          @foreach ($module->lessons as $lesson)
                            @php $locked = ! $isEnrolled && ! $lesson->is_preview; @endphp
                            <li>
                              @if ($lesson->type === 'video')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M10 9l5 3l-5 3z"/></svg>
                              @elseif ($lesson->type === 'quiz')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 16v.01M12 13a2 2 0 0 0 .9-3.8a2 2 0 0 0-2.8 1.3"/></svg>
                              @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2zM9 13h6M9 17h6"/></svg>
                              @endif
                              <span>
                                @if (! $locked)
                                  <a class="text-reset" href="{{ route('player.show', ['course' => $course->slug, 'lesson' => $lesson]) }}">{{ $lesson->getTranslation('title', $locale) }}</a>
                                @else
                                  {{ $lesson->getTranslation('title', $locale) }}
                                @endif
                                @if ($lesson->is_preview)
                                  <span class="badge bg-azure-lt ms-1">Preview</span>
                                @endif
                                @if ($locked)
                                  <span class="visually-hidden">(terkunci)</span>
                                @endif
                              </span>
                              <span class="dur">
                                @if ($locked)
                                  Terkunci &middot;
                                @endif
                                {{ $lesson->duration }} mnt
                              </span>
                            </li>
                          @endforeach
                        </ul>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          <div class="block">
            <h2>Cocok untuk siapa</h2>
            <p>
              @switch($course->level)
                @case('advanced')
                  Praktisi dan pengelola program yang sudah memiliki pengalaman dasar dan ingin memperdalam praktik di lapangan.
                  @break
                @case('intermediate')
                  Amil, relawan, dan pengelola program yang ingin meningkatkan keterampilan teknisnya.
                  @break
                @default
                  Relawan baru, amil pemula, serta siapa saja yang ingin memahami dasar-dasar topik ini. Tidak perlu latar belakang khusus.
              @endswitch
            </p>
          </div>

          <div class="block">
            <h2>Persyaratan</h2>
            @php $req = $course->requirement; @endphp
            <ul class="learn" style="grid-template-columns:1fr">
              <li>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2l4-4"/></svg>
                <span>Perangkat dengan koneksi internet (HP atau komputer)</span>
              </li>
              <li>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2l4-4"/></svg>
                <span>Akun {{ config('app.name') }} (gratis)</span>
              </li>
              <li>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2l4-4"/></svg>
                <span>Nilai asesmen akhir minimal {{ $course->passing_grade }}% untuk mendapat sertifikat</span>
              </li>

              {{-- Kursus prasyarat --}}
              @if ($req?->prerequisiteCourse)
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 0 1 .665 6.479A11.952 11.952 0 0 0 12 20.055a11.952 11.952 0 0 0-6.824-2.998 12.078 12.078 0 0 1 .665-6.479L12 14z"/></svg>
                  <span>
                    Sudah menyelesaikan kursus
                    <strong>
                      <a href="{{ route('catalog.show', $req->prerequisiteCourse->slug) }}" class="text-reset text-decoration-underline">
                        {{ $req->prerequisiteCourse->getTranslation('title', $locale) }}
                      </a>
                    </strong>
                    @if ($req->min_grade)
                      dengan nilai minimal {{ $req->min_grade }}%
                    @endif
                  </span>
                </li>
              @endif

              {{-- Pendidikan minimal --}}
              @if ($req?->pendidikan)
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 9l-10-4L2 9l10 4l10-4v6M6 11.018V16a6 6 0 0 0 12 0v-4.982"/></svg>
                  <span>Pendidikan minimal: <strong>{{ $req->pendidikan->name }}</strong></span>
                </li>
              @endif

              {{-- Batasan usia --}}
              @if ($req?->min_age || $req?->max_age)
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                  <span>
                    @if ($req->min_age && $req->max_age)
                      Usia {{ $req->min_age }}–{{ $req->max_age }} tahun
                    @elseif ($req->min_age)
                      Usia minimal {{ $req->min_age }} tahun
                    @else
                      Usia maksimal {{ $req->max_age }} tahun
                    @endif
                  </span>
                </li>
              @endif

              {{-- Catatan tambahan --}}
              @if ($req?->notes)
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 12h6M9 16h4"/></svg>
                  <span>{{ $req->notes }}</span>
                </li>
              @endif
            </ul>
          </div>

          <div class="block" id="penyusun">
            <h2>Penyusun kursus</h2>
            <article class="author">
              <img class="author-photo" src="{{ $avatar }}" alt="Foto {{ $organizationName }}" width="96" height="96" loading="lazy">
              <div>
                <h3>{{ $organizationName }}</h3>
                <p class="role">Penyelenggara kursus</p>
                <p>Kursus ini disusun dan dikurasi oleh tim {{ $organizationName }} agar materi mudah dipahami dan langsung bisa dipraktikkan.</p>
                <ul class="tags">
                  @if ($course->category)
                    <li>{{ $course->category->getTranslation('name', $locale) }}</li>
                  @endif
                  <li>{{ $levelLabels[$course->level] ?? $course->level }}</li>
                </ul>
              </div>
            </article>
          </div>

          <div class="block">
            <div class="cert-note">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="15" cy="15" r="3"/><path d="M13 17.5V22l2-1.5l2 1.5v-4.5M10 19H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10M6 9h12M6 12h3"/></svg>
              <p><strong>Sertifikat yang bisa diverifikasi.</strong> Setiap sertifikat memiliki kode unik dan QR. Siapa saja bisa memeriksa keasliannya lewat halaman <a href="{{ route('verifikasi') }}">verifikasi sertifikat</a>.</p>
            </div>
          </div>
        </div>

        <!-- ============ KARTU DAFTAR ============ -->
        <aside class="col-lg-4 order-1 order-lg-2" aria-label="Ringkasan dan pendaftaran">
          <div class="enroll">
            @if ($course->thumbnail)
              <img src="{{ route('media.thumbnail', ['path' => $course->thumbnail]) }}" alt="{{ $title }}" width="640" height="360">
            @else
              <img src="{{ asset('images/course-1.webp') }}" alt="" width="640" height="360">
            @endif
            <div class="body">
              <div class="price">{{ $priceLabel }}</div>
              <p class="text-secondary mb-3">{{ $certificateNote }}</p>

              @guest
                <a class="btn btn-primary w-100 mb-2" href="{{ route('register') }}">Daftar kursus</a>
                <a class="btn btn-outline-primary w-100" href="{{ route('login') }}">Sudah punya akun? Masuk</a>
              @endguest

              @auth
                @if ($isEnrolled)
                  <p class="text-secondary mb-2">Kamu sudah terdaftar di kursus ini.</p>
                  @if ($enrollment->progress_percent > 0)
                    <div class="progress progress-bar-nu mb-2"><div class="progress-bar" style="width: {{ $enrollment->progress_percent }}%"></div></div>
                    <p class="text-secondary">{{ $enrollment->progress_percent }}% selesai</p>
                  @endif
                  <a class="btn btn-primary w-100" href="{{ route('learning.dashboard') }}">Lanjutkan belajar</a>
                @elseif ($course->access_type === 'free')
                  <form action="{{ route('enroll.store', $course->slug) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary w-100" type="submit">Daftar kursus ini</button>
                  </form>
                @else
                  <form action="{{ route('enroll.store', $course->slug) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary w-100" type="submit">
                      Daftar &amp; bayar via Midtrans
                    </button>
                  </form>
                  <p class="text-secondary small mt-2 mb-0">
                    Akses dan sertifikat dibuka setelah pembayaran lunas.
                  </p>
                @endif
              @endauth

              <dl>
                <div><dt>Level</dt><dd>{{ $levelLabels[$course->level] ?? $course->level }}</dd></div>
                <div><dt>Durasi</dt><dd>{{ $durationLabel ?? '-' }}</dd></div>
                <div><dt>Modul</dt><dd>{{ $modules->count() }} modul</dd></div>
                <div><dt>Materi</dt><dd>{{ $lessonCount }} materi</dd></div>
                <div><dt>Bahasa</dt><dd>Indonesia</dd></div>
                <div>
                  <dt>Akses</dt>
                  <dd>{{ match ($course->access_type) { 'free' => 'Mandiri, kapan saja', 'donation' => 'Setelah donasi', default => 'Setelah pembayaran' } }}</dd>
                </div>
                <div>
                  <dt>Sertifikat</dt>
                  <dd>{{ match ($course->certificate_type) { 'free' => 'Ya, gratis', 'donation' => 'Ya, via donasi', default => 'Ya, berbayar' } }}</dd>
                </div>
              </dl>

              {{-- Share widget --}}
              @php
                $shareUrl   = urlencode(route('catalog.show', $course->slug));
                $shareText  = urlencode('Yuk ikuti kursus "' . $title . '" di ' . config('app.name') . '!');
              @endphp
              <div class="share-widget" x-data="{ copied: false }">
                <p class="share-label">Bagikan kursus ini</p>
                <div class="share-buttons">
                  {{-- WhatsApp --}}
                  <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                     target="_blank" rel="noopener noreferrer"
                     class="share-btn share-btn--wa" aria-label="Bagikan ke WhatsApp">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                    </svg>
                    <span>WhatsApp</span>
                  </a>

                  {{-- X / Twitter --}}
                  <a href="https://x.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}"
                     target="_blank" rel="noopener noreferrer"
                     class="share-btn share-btn--x" aria-label="Bagikan ke X">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.738l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    <span>X</span>
                  </a>

                  {{-- LinkedIn --}}
                  <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                     target="_blank" rel="noopener noreferrer"
                     class="share-btn share-btn--li" aria-label="Bagikan ke LinkedIn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                    <span>LinkedIn</span>
                  </a>

                  {{-- Copy link --}}
                  <button type="button"
                          class="share-btn share-btn--copy"
                          aria-label="Salin tautan"
                          @click="navigator.clipboard.writeText('{{ route('catalog.show', $course->slug) }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })">
                    <template x-if="!copied">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </template>
                    <template x-if="copied">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    </template>
                    <span x-text="copied ? 'Tersalin!' : 'Salin tautan'"></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta-band course-cta" aria-labelledby="h-cta">
    <div class="container-xl">
      <div class="row g-3 align-items-center">
        <div class="col-lg-8">
          <h2 class="h2 text-white mb-1" id="h-cta">Mulai belajar hari ini</h2>
          <p class="mb-0">Daftar gratis dan selesaikan kursus dengan ritme Anda sendiri.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
          @auth
            <a class="btn btn-accent btn-lg" href="{{ route('learning.dashboard') }}">Lanjutkan belajar</a>
          @else
            <a class="btn btn-accent btn-lg" href="{{ route('register') }}">Daftar gratis</a>
          @endauth
        </div>
      </div>
    </div>
  </section>

  @push('head')
  <script type="application/ld+json">
  @php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $title,
        'description' => $summary,
        'provider' => ['@type' => 'Organization', 'name' => $organizationName],
        'inLanguage' => $locale,
        'courseMode' => 'online',
        'timeRequired' => $course->duration_minutes ? 'PT'.intdiv($course->duration_minutes, 60).'H'.($course->duration_minutes % 60).'M' : null,
        'offers' => ['@type' => 'Offer', 'price' => $course->access_type === 'free' ? 0 : $course->min_amount, 'priceCurrency' => $course->currency ?? 'IDR'],
    ];
  @endphp
  {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>
  <link rel="alternate" hreflang="id" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('id', route('catalog.show', $course->slug)) }}">
  <link rel="alternate" hreflang="en" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', route('catalog.show', $course->slug)) }}">
  <link rel="alternate" hreflang="x-default" href="{{ route('catalog.show', $course->slug) }}">
  @endpush

</x-layouts.public>

