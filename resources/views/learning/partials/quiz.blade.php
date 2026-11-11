<div class="card mb-4">
  <div class="card-body">
    <h2 class="h3">{{ $lesson->getTranslation('title', app()->getLocale()) }}</h2>

    @if ($textHtml !== '')
      <div class="text-secondary mb-3">{!! nl2br(e(strip_tags($textHtml))) !!}</div>
    @endif

    <p class="text-secondary">
      Jawab semua pertanyaan. Nilai minimal untuk lulus adalah
      <strong>{{ $passingScore }}</strong>.
    </p>

    @if ($lastAttempt)
      <div class="alert {{ $lastAttempt->passed ? 'alert-success' : 'alert-warning' }}" role="status">
        Percobaan terakhir: skor <strong>{{ $lastAttempt->score }}</strong>
        ({{ $lastAttempt->passed ? 'lulus' : 'belum lulus' }}).
      </div>
    @endif

    @if (! $enrollment)
      <div class="alert alert-info mb-0">
        Daftar kursus ini untuk mengikuti asesmen dan mendapatkan sertifikat.
      </div>
    @elseif (! $quiz || $quiz->questions->isEmpty())
      <div class="alert alert-secondary mb-0">
        Asesmen belum tersedia. Hubungi pengelola kursus.
      </div>
    @elseif ($completedLessonIds->contains($lesson->id))
      <div class="alert alert-success">
        Asesmen sudah diselesaikan.
      </div>

      <div class="d-flex flex-wrap gap-2">
        @if ($nextLesson)
          <a class="btn btn-primary" href="{{ route('player.show', ['course' => $course->slug, 'lesson' => $nextLesson->uuid]) }}">
            Materi berikutnya
          </a>
        @endif
        @if ($enrollment->certificate)
          <a class="btn btn-success" href="{{ route('certificates.show', $enrollment->certificate->code) }}">
            Lihat sertifikat
          </a>
        @endif
      </div>

      @php
        $nextPostTestModule = $course->modules
          ->sortBy('sort')
          ->values()
          ->first(fn ($m) => $m->sort > $lesson->module->sort && $m->type === 'post_test');
      @endphp
      @if ($nextPostTestModule)
        @php
          $ptQuiz = $nextPostTestModule->quizzes->first();
          $postTestPassed = $ptQuiz ? $enrollment->quizAttempts()->where('quiz_id', $ptQuiz->id)->where('passed', true)->exists() : false;
        @endphp
        @if ($ptQuiz && ! $postTestPassed)
          <a class="btn btn-primary" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(app()->getLocale(), route('module.posttest', ['course' => $course->slug, 'module' => $nextPostTestModule->uuid])) }}">
            Lanjut ke Post-test
          </a>
        @endif
      @endif
    @else
      <form method="POST" action="{{ route('player.quiz.submit', ['course' => $course->slug, 'lesson' => $lesson->uuid]) }}">
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
    @endif
  </div>
</div>
