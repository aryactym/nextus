<x-layouts.public
    :title="($type === 'pre' ? 'Pre-test' : 'Post-test') . ' — ' . $module->getTranslation('title', app()->getLocale())"
    :description="$course->getTranslation('title', app()->getLocale())"
    active-page="belajar"
>
@php
    $locale = app()->getLocale();
    $moduleTitle = $module->getTranslation('title', $locale);
@endphp

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('learning.dashboard') }}">Belajar saya</a></li>
        <li><a href="{{ route('catalog.show', $course->slug) }}">{{ $course->getTranslation('title', $locale) }}</a></li>
        <li aria-current="page">{{ $type === 'pre' ? 'Pre-test' : 'Post-test' }}: {{ $moduleTitle }}</li>
      </ol>
      <h1>{{ $type === 'pre' ? 'Pre-test' : 'Post-test' }}: {{ $moduleTitle }}</h1>
      <p class="text-secondary">
        {{ $type === 'pre' ? 'Selesaikan pre-test ini sebelum memulai materi modul.' : 'Selesaikan post-test ini untuk menyelesaikan modul.' }}
      </p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      @if (session('quiz_passed'))
        <div class="alert alert-success" role="status">{{ session('quiz_passed') }}</div>
      @endif
      @if (session('quiz_failed'))
        <div class="alert alert-warning" role="status">{{ session('quiz_failed') }}</div>
      @endif

      <div class="row g-4">
        <div class="col-lg-8">
          @if (! $enrollment)
            <div class="alert alert-info">
              Daftar kursus ini untuk mengikuti asesmen dan mendapatkan sertifikat.
            </div>
          @elseif ($lastAttempt && $lastAttempt->passed && $type === 'post')
            <div class="alert alert-success">
              Post-test sudah lulus dengan skor <strong>{{ $lastAttempt->score }}</strong>.
            </div>
            <a class="btn btn-primary" href="{{ route('catalog.show', $course->slug) }}">Kembali ke kursus</a>
          @elseif (! $quiz || $quiz->questions->isEmpty())
            <div class="alert alert-secondary">
              Asesmen belum tersedia. Hubungi pengelola kursus.
            </div>
          @else
            @if ($lastAttempt)
              <div class="alert {{ $lastAttempt->passed ? 'alert-success' : 'alert-warning' }}" role="status">
                Percobaan terakhir: skor <strong>{{ $lastAttempt->score }}</strong>
                ({{ $lastAttempt->passed ? 'lulus' : 'belum lulus' }}).
              </div>
            @endif

            @if ($type === 'post')
              <p class="text-secondary">
                Jawab semua pertanyaan. Nilai minimal untuk lulus adalah <strong>{{ $passingScore }}</strong>.
              </p>
            @else
              <p class="text-secondary">
                Jawab semua pertanyaan. Pre-test tidak memiliki nilai minimal kelulusan.
              </p>
            @endif

            @php $maxAttemptsDisplay = $maxAttempts ?? 1; @endphp
            <p class="text-secondary small">
              Percobaan: <strong>{{ $attemptCount }}</strong> / <strong>{{ $maxAttemptsDisplay }}</strong>
            </p>

            @if ($attemptCount < ($maxAttempts ?? 1))
            <form method="POST" action="{{ $type === 'pre' ? route('module.pretest.submit', ['course' => $course->slug, 'module' => $module->uuid]) : route('module.posttest.submit', ['course' => $course->slug, 'module' => $module->uuid]) }}">
              @csrf

              @foreach ($quiz->questions as $questionIndex => $question)
                <fieldset class="mb-4">
                  <legend class="h4 mb-2">
                    {{ $questionIndex + 1 }}. {{ $question->question }}
                  </legend>

                  @foreach ($question->options as $optionIndex => $option)
                    <label class="form-check">
                      <input
                        class="form-check-input"
                        type="radio"
                        name="answers[{{ $question->id }}]"
                        value="{{ $optionIndex }}"
                        required
                      >
                      <span class="form-check-label">{{ $option }}</span>
                    </label>
                  @endforeach

                  @error('answers.'.$question->id)
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </fieldset>
              @endforeach

              <button type="submit" class="btn btn-primary">Kirim jawaban</button>
            </form>
            @else
              <div class="alert alert-danger" role="alert">
                Batas percobaan {{ $type === 'pre' ? 'pre-test' : 'post-test' }} ({{ $maxAttemptsDisplay }}) telah tercapai.
              </div>
            @endif
          @endif
        </div>
      </div>
    </div>
  </section>
@auth
  @unless(auth()->user()->can('manage organizations'))
    <style>
      body { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
    </style>
    <script>
      (function () {
        document.addEventListener('contextmenu', function (e) { e.preventDefault(); });
        document.addEventListener('copy', function (e) { e.preventDefault(); });
        document.addEventListener('cut', function (e) { e.preventDefault(); });
        document.addEventListener('paste', function (e) { e.preventDefault(); });
        document.addEventListener('keydown', function (e) {
          var key = (e.key || '').toLowerCase();
          if ((e.ctrlKey || e.metaKey) && ['c', 'v', 'x', 'p', 's', 'u', 'a'].includes(key)) {
            e.preventDefault();
            e.stopPropagation();
          }
          if (key === 'printscreen' || e.key === 'PrintScreen') {
            e.preventDefault();
            e.stopPropagation();
            if (navigator.clipboard && navigator.clipboard.writeText) {
              navigator.clipboard.writeText('').catch(function () {});
            }
            window.getSelection && window.getSelection().removeAllRanges();
          }
        }, true);
        document.addEventListener('keyup', function (e) {
          if ((e.key || '').toLowerCase() === 'printscreen') {
            if (navigator.clipboard && navigator.clipboard.writeText) {
              navigator.clipboard.writeText('').catch(function () {});
            }
          }
        }, true);
      })();
    </script>
  @endunless
@endauth
</x-layouts.public>
