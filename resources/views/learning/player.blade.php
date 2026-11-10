<x-layouts.public
    :title="$lesson->getTranslation('title', app()->getLocale())"
    :description="$course->getTranslation('title', app()->getLocale())"
    active-page="belajar"
>

@php
    $locale = app()->getLocale();
    $lessonTitle = $lesson->getTranslation('title', $locale);
    $translations = $lesson->getTranslations('content');
    $content = is_array($translations) ? $translations : [];
    $textHtml = $content[$locale] ?? $content['id'] ?? '';
    // Fallback ke bahasa Indonesia bila konten bahasa saat ini kosong / tidak diisi
    if ($locale !== 'id' && trim((string) $textHtml) === '') {
        $textHtml = $content['id'] ?? '';
    }
    $percent = $totalLessons > 0 ? (int) round($completedLessonIds->count() / $totalLessons * 100) : 0;
@endphp

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('learning.dashboard') }}">Belajar saya</a></li>
        <li><a href="{{ route('catalog.show', $course->slug) }}">{{ $course->getTranslation('title', $locale) }}</a></li>
        <li aria-current="page">{{ $lessonTitle }}</li>
      </ol>
      <h1>{{ $lessonTitle }}</h1>
      <div class="lesson-meta">
        @if ($lesson->is_preview)
          <span class="badge bg-secondary-lt">Preview</span>
        @endif
        @if ($lesson->duration)
          <span class="badge bg-secondary-lt">{{ $lesson->duration }} menit</span>
        @endif
        @if ($enrollment)
          <span class="badge bg-green-lt">{{ $percent }}% selesai</span>
        @endif
      </div>
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
      @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
      @endif

      <div class="row g-4">

        <div class="col-lg-8">
          @if ($lesson->type === 'quiz')
            @include('learning.partials.quiz', ['textHtml' => $textHtml])
          @else
            <x-media-player
              :type="$lesson->type"
              :video-url="$lesson->video_url"
              :content="$textHtml"
              :file-url="$fileUrl"
              :title="$lessonTitle"
            />
          @endif

          @if ($enrollment && $lesson->type !== 'quiz')
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
              @if ($previousLesson)
                <a class="btn btn-outline-primary" href="{{ route('player.show', ['course' => $course->slug, 'lesson' => $previousLesson->uuid]) }}">Sebelumnya</a>
              @endif

              <div class="d-flex flex-wrap gap-2">
                @if (!$completedLessonIds->contains($lesson->id))
                  <form action="{{ route('player.complete', ['course' => $course->slug, 'lesson' => $lesson->uuid]) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary" type="submit">Tandai selesai</button>
                  </form>
                @else
                  <span class="badge bg-green-lt">Sudah selesai</span>
                @endif

                @if ($nextLesson)
                  <a class="btn btn-primary" href="{{ route('player.show', ['course' => $course->slug, 'lesson' => $nextLesson->uuid]) }}">Berikutnya</a>
                @endif
              </div>
            </div>
          @endif
        </div>

        <aside class="col-lg-4" aria-label="Kurikulum" style="position: sticky; top: 1rem; max-height: calc(100vh - 2rem); overflow-y: auto; display: block;">
          <div class="card">
            <div class="card-body">
              <h2 class="h3">Kurikulum</h2>
              @if ($enrollment)
                <div class="progress progress-bar-nu mb-3"><div class="progress-bar" style="width: {{ $percent }}%"></div></div>
              @endif
              <div class="accordion" id="curriculum">
                @foreach ($course->modules as $module)
                  @php $moduleActive = $module->lessons->contains(fn ($l) => $l->id === $lesson->id); @endphp
                  <div class="accordion-item{{ $moduleActive ? ' module-active' : '' }}">
                    <h3 class="accordion-header">
                      <button
                        class="accordion-button{{ !$moduleActive ? ' collapsed' : '' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#module-{{ $module->id }}"
                        aria-expanded="{{ $moduleActive ? 'true' : 'false' }}"
                        aria-controls="module-{{ $module->id }}"
                      >
                        {{ $module->getTranslation('title', $locale) }}
                      </button>
                    </h3>
                    <div id="module-{{ $module->id }}" class="accordion-collapse collapse{{ $moduleActive ? ' show' : '' }}" data-bs-parent="#curriculum">
                      <ul class="list-group list-group-flush">
                        @foreach ($module->lessons as $item)
                          @php $locked = ! $enrollment && ! $item->is_preview; $lessonActive = $item->id === $lesson->id; @endphp
                          <li class="list-group-item d-flex align-items-start gap-2{{ $lessonActive ? ' lesson-active' : '' }}">
                            @if ($locked)
                              <span class="text-secondary">{{ $item->getTranslation('title', $locale) }} &middot; terkunci</span>
                            @else
                              <a
                                href="{{ route('player.show', ['course' => $course->slug, 'lesson' => $item->uuid]) }}"
                                class="d-block w-100 text-reset text-decoration-none {{ $lessonActive ? 'fw-bold' : '' }}"
                                @if ($lessonActive) aria-current="step" @endif
                              >
                                <span class="lesson-title">{{ $item->getTranslation('title', $locale) }}</span>
                                @if ($completedLessonIds->contains($item->id))
                                  <span class="badge bg-green-lt ms-auto">Selesai</span>
                                @endif
                                @if ($item->is_preview)
                                  <span class="badge bg-azure-lt ms-1">Preview</span>
                                @endif
                              </a>
                            @endif
                          </li>
                        @endforeach
                      </ul>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>

</x-layouts.public>
