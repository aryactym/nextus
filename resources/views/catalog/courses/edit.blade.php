@extends('layouts.app')

@php
    $accessOptions = ['free' => 'Gratis', 'donation' => 'Donasi', 'paid' => 'Berbayar'];
    $lessonBadges = ['text' => 'bg-green-lt', 'video' => 'bg-blue-lt', 'pdf' => 'bg-orange-lt', 'quiz' => 'bg-purple-lt'];
    $courseTitle = $course->getTranslation('title', 'id') ?: 'Ubah kursus';
@endphp

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Katalog / Kursus</div>
            <h2 class="page-title">{{ $courseTitle }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none d-flex gap-2">
            @if ($course->status === 'published')
                <a href="{{ route('catalog.show', $course->slug) }}" target="_blank" rel="noopener"
                   class="btn btn-outline-secondary">Lihat halaman</a>
            @endif
            <a href="{{ route('admin.courses.index') }}" class="btn btn-link">Kembali</a>        </div>
    </div>
</div>


        <form method="POST" action="{{ route('admin.courses.update', $course->uuid) }}" enctype="multipart/form-data" id="course-form">
    @csrf
    @method('PUT')
    {{-- Form ini HANYA membungkus card Informasi kursus. Field di sidebar menggunakan
         HTML form="" attribute agar tetap terkirim ke course-form tanpa nested form. --}}

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Informasi kursus</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="title_id">Judul (Indonesia)</label>
                            <input type="text" id="title_id" name="title[id]"
                                   class="form-control @error('title.id') is-invalid @enderror"
                                   value="{{ old('title.id', $course->getTranslation('title', 'id')) }}" required>
                            @error('title.id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="title_en">Judul (Inggris)</label>
                            <input type="text" id="title_en" name="title[en]"
                                   class="form-control @error('title.en') is-invalid @enderror"
                                   value="{{ old('title.en', $course->getTranslation('title', 'en')) }}">
                            @error('title.en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" id="slug" name="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $course->slug) }}" required>
                        <div class="form-hint">Dipakai pada URL publik: <code>/kursus/{{ old('slug', $course->slug) }}</code>.</div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label" for="category_id">Kategori</label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Tanpa kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        @selected((string) old('category_id', $course->category_id) === (string) $category->id)>
                                        {{ $category->getTranslation('name', 'id') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="level">Level</label>
                            <select id="level" name="level"
                                    class="form-select">
                                <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>
                                    Beginner
                                </option>
                                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>
                                    Intermediate
                                </option>
                                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>
                                    Advanced
                                </option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="duration_minutes">Durasi (menit)</label>
                            <input type="number" id="duration_minutes" name="duration_minutes"
                                   class="form-control @error('duration_minutes') is-invalid @enderror"
                                   value="{{ old('duration_minutes', $course->duration_minutes) }}" min="0">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="summary_id">Ringkasan (Indonesia)</label>
                        <textarea id="summary_id" name="summary[id]" rows="2"
                                  class="form-control @error('summary.id') is-invalid @enderror">{{ old('summary.id', $course->getTranslation('summary', 'id')) }}</textarea>
                        <div class="form-hint">Tampil singkat di kartu katalog.</div>
                        @error('summary.id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                   <div class="mb-0">
                        <label class="form-label" for="description_id">Deskripsi (Indonesia)</label>
                        <textarea id="description_id" name="description[id]" rows="6"
                                  class="form-control @error('description.id') is-invalid @enderror">{{ old('description.id', $course->getTranslation('description', 'id')) }}</textarea>
                        @error('description.id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </form>{{-- #course-form ditutup di sini --}}

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Modul &amp; pelajaran</h3>
                    <div class="card-actions">
                        <span class="badge bg-secondary-lt">{{ $course->modules->count() }} modul</span>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($course->modules as $module)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>


                                    <h4 class="mb-1">
                                        {{ $module->getTranslation('title', 'id') }}
                                        @if ($module->type === 'pre_test')
                                            <span class="badge bg-warning-lt ms-2">Pre-test</span>
                                        @elseif ($module->type === 'post_test')
                                            <span class="badge bg-success-lt ms-2">Post-test</span>
                                        @endif
                                    </h4>
                                    <div class="text-secondary small">{{ $module->lessons->count() }} pelajaran</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#module-edit-{{ $module->uuid }}"
                                            aria-expanded="false" aria-controls="module-edit-{{ $module->uuid }}">
                                        Ubah modul
                                    </button>
                                    <form action="{{ route('admin.modules.destroy', $module) }}" method="POST"
                                          onsubmit="return confirm('Hapus modul ini beserta pelajarannya?')" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus modul</button>
                                    </form>                                </div>
                            </div>

                            {{-- Module edit form (separate, not nested) --}}
                            <div class="collapse mt-3" id="module-edit-{{ $module->uuid }}">
                                <form action="{{ route('admin.modules.update', $module) }}" method="POST" class="row g-2 align-items-end">
                                    @csrf @method('PUT')
                                    <div class="col-md-5">
                                        <label class="form-label" for="module-title-{{ $module->uuid }}">Judul (Indonesia)</label>
                                        <input type="text" id="module-title-{{ $module->uuid }}" name="title[id]"
                                               class="form-control" value="{{ $module->getTranslation('title', 'id') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="module-title-en-{{ $module->uuid }}">Judul (Inggris)</label>
                                        <input type="text" id="module-title-en-{{ $module->uuid }}" name="title[en]"
                                               class="form-control" value="{{ $module->getTranslation('title', 'en') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label" for="module-sort-{{ $module->uuid }}">Urutan</label>
                                        <input type="number" id="module-sort-{{ $module->uuid }}" name="sort"
                                               class="form-control" value="{{ $module->sort }}" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">Simpan modul</button>
                                    </div>
                                </form>
                            </div>

                            @if (in_array($module->type, ['pre_test', 'post_test'], true))
                                {{-- Quiz langsung untuk modul pre-test/post-test --}}
                                @php
                                    $testQuiz = $module->quizzes->first();
                                    $testQuestions = $testQuiz?->questions ?? collect();
                                    $testType = $module->type === 'pre_test' ? 'pre' : 'post';
                                    $testLabel = $module->type === 'pre_test' ? 'Pre-test' : 'Post-test';
                                @endphp
                                <div class="border rounded p-3 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">{{ $testLabel }} ({{ $testQuestions->count() }} soal)</h5>
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#{{ $testType }}-form-{{ $module->uuid }}"
                                                aria-expanded="false" aria-controls="{{ $testType }}-form-{{ $module->uuid }}">
                                            Tambah soal
                                        </button>
                                    </div>

                                    @forelse ($testQuestions as $question)
                                        <div class="border rounded p-2 mb-2 bg-white">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <strong>{{ $question->question }}</strong>
                                                    <ol class="mb-0 small mt-1">
                                                        @foreach ($question->options as $optionIndex => $option)
                                                            <li class="{{ $optionIndex === $question->correct_index ? 'text-success fw-bold' : '' }}">
                                                                {{ $option }}
                                                                @if ($optionIndex === $question->correct_index)
                                                                    (benar)
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                                <form action="{{ route('admin.quiz-questions.destroy', $question) }}" method="POST"
                                                      onsubmit="return confirm('Hapus soal ini?')" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-secondary small mb-2">Belum ada soal. Klik "Tambah soal" untuk menambahkan.</p>
                                    @endforelse

                                    <div class="collapse mt-2" id="{{ $testType }}-form-{{ $module->uuid }}">
                                        <form action="{{ route('admin.modules.quiz.questions.store', [$module, $testType]) }}" method="POST" class="row g-2">
                                            @csrf
                                            <div class="col-12">
                                                <label class="form-label" for="{{ $testType }}-question-{{ $module->uuid }}">Pertanyaan</label>
                                                <textarea id="{{ $testType }}-question-{{ $module->uuid }}" name="question" class="form-control" rows="2" required></textarea>
                                            </div>
                                            <div class="row g-2 mb-2">
                                                @for ($optionIndex = 0; $optionIndex < 4; $optionIndex++)
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="{{ $testType }}-option-{{ $module->uuid }}-{{ $optionIndex }}">
                                                            Pilihan {{ $optionIndex + 1 }}
                                                        </label>
                                                        <input type="text" id="{{ $testType }}-option-{{ $module->uuid }}-{{ $optionIndex }}"
                                                               name="options[{{ $optionIndex }}]" class="form-control" required>
                                                    </div>
                                                @endfor
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label" for="{{ $testType }}-correct-{{ $module->uuid }}">Jawaban benar</label>
                                                <select id="{{ $testType }}-correct-{{ $module->uuid }}" name="correct_index" class="form-select">
                                                    @for ($optionIndex = 0; $optionIndex < 4; $optionIndex++)
                                                        <option value="{{ $optionIndex }}">Pilihan {{ $optionIndex + 1 }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">Tambah soal</button>
                                        </form>
                                    </div>
                                </div>
                            @elseif ($module->lessons->isNotEmpty())
                                <div class="list-group list-group-flush my-3">
                                    @foreach ($module->lessons as $lesson)
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge {{ $lessonBadges[$lesson->type] ?? 'bg-secondary-lt' }}">
                                                        {{ ucfirst($lesson->type) }}
                                                    </span>
                                                    <span>{{ $lesson->getTranslation('title', 'id') }}</span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#lesson-edit-{{ $lesson->uuid }}"
                                                            aria-expanded="false" aria-controls="lesson-edit-{{ $lesson->uuid }}">
                                                        Ubah
                                                    </button>
                                                    <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST"
                                                          onsubmit="return confirm('Hapus pelajaran ini?')" style="display:inline;">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- Lesson edit form (separate, not nested in course form) --}}
                                            <div class="collapse mt-3" id="lesson-edit-{{ $lesson->uuid }}">
                                                <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data" class="row g-2"
                                                      x-data="{ showEn: {{ $lesson->getTranslation('content', 'en') ? 'true' : 'false' }} }"
                                                      @change="showEn = $event.target.checked">
                                                    @csrf @method('PUT')
                                                    <div class="col-md-5">
                                                        <label class="form-label" for="lesson-title-{{ $lesson->uuid }}">Judul (Indonesia)</label>
                                                        <input type="text" id="lesson-title-{{ $lesson->uuid }}" name="title[id]"
                                                               class="form-control" value="{{ $lesson->getTranslation('title', 'id') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="lesson-title-en-{{ $lesson->uuid }}">Judul (Inggris)</label>
                                                        <input type="text" id="lesson-title-en-{{ $lesson->uuid }}" name="title[en]"
                                                               class="form-control" value="{{ $lesson->getTranslation('title', 'en') }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="lesson-type-{{ $lesson->uuid }}">Tipe</label>
                                                        <select id="lesson-type-{{ $lesson->uuid }}" name="type" class="form-select" required>
                                                            @foreach (['text' => 'Teks', 'video' => 'Video', 'pdf' => 'PDF', 'quiz' => 'Kuis'] as $typeValue => $typeLabel)
                                                                <option value="{{ $typeValue }}" @selected($lesson->type === $typeValue)>{{ $typeLabel }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input"
                                                                   x-model="showEn"
                                                                   @if ($lesson->getTranslation('content', 'en')) checked @endif>
                                                            <span class="form-check-label">Tampilkan konten bahasa Inggris</span>
                                                        </label>
                                                    </div>
                                                    <div :class="showEn ? 'col-md-6' : 'col-md-12'">
                                                        <x-rich-editor name="content[id]"
                                                                        value="{{ $lesson->getTranslation('content', 'id') }}"
                                                                        label="Konten (Indonesia)" />
                                                    </div>
                                                    <div class="col-md-6" x-show="showEn" x-cloak>
                                                        <x-rich-editor name="content[en]"
                                                                        value="{{ $lesson->getTranslation('content', 'en') }}"
                                                                        label="Konten (Inggris)" />
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label" for="lesson-video-{{ $lesson->uuid }}">URL video</label>
                                                        <input type="url" id="lesson-video-{{ $lesson->uuid }}" name="video_url"
                                                               class="form-control" value="{{ $lesson->video_url }}" placeholder="https://...">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="lesson-file-{{ $lesson->uuid }}">File PDF</label>
                                                        <input type="file" id="lesson-file-{{ $lesson->uuid }}" name="file"
                                                               class="form-control" accept="application/pdf">
                                                        @if ($lesson->file_path)
                                                            <div class="form-hint">File saat ini: {{ basename($lesson->file_path) }}. Kosongkan jika tidak diubah.</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="lesson-duration-{{ $lesson->uuid }}">Durasi</label>
                                                        <div class="input-group">
                                                            <input type="number" id="lesson-duration-{{ $lesson->uuid }}" name="duration"
                                                                   class="form-control" value="{{ $lesson->duration }}" min="0">
                                                            <span class="input-group-text">mnt</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="lesson-sort-{{ $lesson->uuid }}">Urutan</label>
                                                        <input type="number" id="lesson-sort-{{ $lesson->uuid }}" name="sort"
                                                               class="form-control" value="{{ $lesson->sort }}" min="0">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-check">
                                                            <input type="checkbox" name="is_preview" value="1" class="form-check-input"
                                                                   @checked($lesson->is_preview)>
                                                            <span class="form-check-label">Jadikan preview gratis</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary btn-sm">Simpan pelajaran</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        @if ($lesson->type === 'quiz')
                                            @php
                                                $quiz = $lesson->quiz;
                                                $questions = $quiz?->questions ?? collect();
                                            @endphp
                                            <div class="list-group-item px-0">
                                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#quiz-{{ $lesson->uuid }}"
                                                        aria-expanded="false" aria-controls="quiz-{{ $lesson->uuid }}">
                                                    Kelola soal kuis ({{ $questions->count() }})
                                                </button>

                                                <div class="collapse mt-2" id="quiz-{{ $lesson->uuid }}">
                                                    @forelse ($questions as $question)
                                                        <div class="border rounded p-2 mb-2">
                                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                                <div>
                                                                    <strong>{{ $question->question }}</strong>
                                                                    <ol class="mb-0 small mt-1">
                                                                        @foreach ($question->options as $optionIndex => $option)
                                                                            <li class="{{ $optionIndex === $question->correct_index ? 'text-success fw-bold' : '' }}">
                                                                                {{ $option }}
                                                                                @if ($optionIndex === $question->correct_index)
                                                                                    (benar)
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ol>
                                                                </div>
                                                                <div class="d-flex gap-2">
                                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                                            data-bs-toggle="collapse" data-bs-target="#question-edit-{{ $question->uuid }}"
                                                                            aria-expanded="false" aria-controls="question-edit-{{ $question->uuid }}">
                                                                        Ubah
                                                                    </button>
                                                                    <form action="{{ route('admin.quiz-questions.destroy', $question) }}" method="POST"
                                                                          onsubmit="return confirm('Hapus soal ini?')" style="display:inline;">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                            @php
                                                                $editOptionCount = max(4, count($question->options));
                                                                $editOptions = array_pad(array_values($question->options), $editOptionCount, '');
                                                            @endphp
                                                            <div class="collapse mt-2" id="question-edit-{{ $question->uuid }}">
                                                                <form action="{{ route('admin.quiz-questions.update', $question) }}" method="POST">
                                                                    @csrf @method('PUT')
                                                                    <div class="mb-2">
                                                                        <label class="form-label" for="question-text-{{ $question->uuid }}">Pertanyaan</label>
                                                                        <textarea id="question-text-{{ $question->uuid }}" name="question"
                                                                                  class="form-control" rows="2" required>{{ $question->question }}</textarea>
                                                                    </div>
                                                                    <div class="row g-2 mb-2">
                                                                        @for ($optionIndex = 0; $optionIndex < $editOptionCount; $optionIndex++)
                                                                            <div class="col-md-6">
                                                                                <label class="form-label" for="question-option-{{ $question->uuid }}-{{ $optionIndex }}">
                                                                                    Pilihan {{ $optionIndex + 1 }}
                                                                                </label>
                                                                                <input type="text" id="question-option-{{ $question->uuid }}-{{ $optionIndex }}"
                                                                                       name="options[{{ $optionIndex }}]" class="form-control"
                                                                                       value="{{ $editOptions[$optionIndex] ?? '' }}">
                                                                            </div>
                                                                        @endfor
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label" for="question-correct-{{ $question->uuid }}">Jawaban benar</label>
                                                                        <select id="question-correct-{{ $question->uuid }}" name="correct_index" class="form-select">
                                                                            @for ($optionIndex = 0; $optionIndex < $editOptionCount; $optionIndex++)
                                                                                <option value="{{ $optionIndex }}" @selected($question->correct_index === $optionIndex)>
                                                                                    Pilihan {{ $optionIndex + 1 }}
                                                                                </option>
                                                                            @endfor
                                                                        </select>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-primary btn-sm">Simpan soal</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-secondary small">Belum ada soal. Tambahkan soal agar asesmen bisa dikerjakan.</p>
                                                    @endforelse

                                                    <form action="{{ route('admin.lessons.quiz.questions.store', $lesson) }}" method="POST">
                                                        @csrf
                                                        <div class="mb-2">
                                                            <label class="form-label" for="question-{{ $lesson->uuid }}">Pertanyaan</label>
                                                            <textarea id="question-{{ $lesson->uuid }}" name="question" class="form-control" rows="2" required></textarea>
                                                        </div>
                                                        <div class="row g-2 mb-2">
                                                            @for ($optionIndex = 0; $optionIndex < 4; $optionIndex++)
                                                                <div class="col-md-6">
                                                                    <label class="form-label" for="option-{{ $lesson->uuid }}-{{ $optionIndex }}">
                                                                        Pilihan {{ $optionIndex + 1 }}
                                                                    </label>
                                                                    <input type="text" id="option-{{ $lesson->uuid }}-{{ $optionIndex }}"
                                                                           name="options[{{ $optionIndex }}]" class="form-control" required>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label" for="correct-{{ $lesson->uuid }}">Jawaban benar</label>
                                                            <select id="correct-{{ $lesson->uuid }}" name="correct_index" class="form-select">
                                                                @for ($optionIndex = 0; $optionIndex < 4; $optionIndex++)
                                                                    <option value="{{ $optionIndex }}">Pilihan {{ $optionIndex + 1 }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-sm">Tambah soal</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @if ($module->type === 'content')
                            <a class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse"
                               href="#lesson-form-{{ $module->uuid }}" role="button"
                               aria-expanded="false" aria-controls="lesson-form-{{ $module->uuid }}">
                                Tambah pelajaran
                            </a>

                            <div class="collapse mt-3" id="lesson-form-{{ $module->uuid }}">
                                <form action="{{ route('admin.modules.lessons.store', $module) }}" method="POST" enctype="multipart/form-data"
                                      x-data="{ showEn: false }">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <label class="form-label">Judul (Indonesia)</label>
                                            <input type="text" name="title[id]" class="form-control" placeholder="Judul pelajaran" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Judul (Inggris)</label>
                                            <input type="text" name="title[en]" class="form-control" placeholder="Lesson title">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Tipe</label>
                                            <select name="type" class="form-select">
                                                <option value="text">Teks</option>
                                                <option value="video">Video</option>
                                                <option value="pdf">PDF</option>
                                                <option value="quiz">Kuis</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" x-model="showEn">
                                                <span class="form-check-label">Tampilkan konten bahasa Inggris</span>
                                            </label>
                                        </div>
                                        <div :class="showEn ? 'col-md-6' : 'col-md-12'">
                                            <x-rich-editor name="content[id]" label="Konten (Indonesia)" rows="2" />
                                        </div>
                                        <div class="col-md-6" x-show="showEn" x-cloak>
                                            <x-rich-editor name="content[en]" label="Konten (Inggris)" rows="2" />
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">URL video</label>
                                            <input type="url" name="video_url" class="form-control" placeholder="https://...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">File PDF</label>
                                            <input type="file" name="file" class="form-control" accept="application/pdf">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Durasi</label>
                                            <div class="input-group">
                                                <input type="number" name="duration" class="form-control" min="0" placeholder="0">
                                                <span class="input-group-text">mnt</span>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="form-check mt-2">
                                        <input type="checkbox" name="is_preview" value="1" class="form-check-input">
                                        <span class="form-check-label">Jadikan preview gratis</span>
                                    </label>
                                    <button class="btn btn-primary btn-sm mt-2">Simpan pelajaran</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-secondary py-4">
                            Belum ada modul. Tambahkan modul pertama di bawah.
                        </div>
                    @endforelse

                    <hr>

                    <h4>Tambah modul</h4>
                    <form action="{{ route('admin.courses.modules.store', $course->uuid) }}" method="POST" class="row g-2">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label visually-hidden" for="new_module_title">Judul modul baru</label>
                            <input type="text" id="new_module_title" name="title[id]" class="form-control"
                                   placeholder="Judul modul baru" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label visually-hidden" for="new_module_type">Tipe modul</label>
                            <select id="new_module_type" name="type" class="form-select">
                                <option value="content">Materi</option>
                                <option value="pre_test">Pre-test</option>
                                <option value="post_test">Post-test</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100">Tambah modul</button>
                        </div>
                                    </form>
                                </div>
                            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Publikasi</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" name="status" form="course-form" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="draft" @selected(old('status', $course->status) === 'draft')>Draf</option>
                            <option value="published" @selected(old('status', $course->status) === 'published')>Terbit</option>
                            <option value="archived" @selected(old('status', $course->status) === 'archived')>Arsip</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="access_type">Tipe akses</label>
                        <select id="access_type" name="access_type" form="course-form" class="form-select @error('access_type') is-invalid @enderror" required>
                            @foreach ($accessOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('access_type', $course->access_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('access_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="certificate_type">Tipe sertifikat</label>
                        <select id="certificate_type" name="certificate_type" form="course-form" class="form-select @error('certificate_type') is-invalid @enderror" required>
                            @foreach ($accessOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('certificate_type', $course->certificate_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('certificate_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="passing_grade">Passing grade</label>
                        <div class="input-group">
                            <input type="number" id="passing_grade" name="passing_grade" form="course-form"
                                   class="form-control @error('passing_grade') is-invalid @enderror"
                                   value="{{ old('passing_grade', $course->passing_grade) }}" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                        @error('passing_grade')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Thumbnail</label>
                        @if ($course->thumbnail)
                            <img src="{{ \Illuminate\Support\Facades\URL::signedRoute('admin.uploads.show', ['path' => $course->thumbnail]) }}"
                                 class="img-fluid rounded border mb-2" alt="Thumbnail kursus">
                        @else
                            <div class="text-secondary mb-2">Belum ada thumbnail.</div>
                        @endif
                        <input type="file" name="thumbnail" form="course-form" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                        <div class="form-hint">JPG, PNG, atau WebP. Maksimal 2 MB.</div>
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Latar belakang sertifikat</label>
                        @if ($course->certificate_background)
                            <img src="{{ \Illuminate\Support\Facades\URL::signedRoute('admin.uploads.show', ['path' => $course->certificate_background]) }}"
                                 class="img-fluid rounded border mb-2" alt="Latar belakang sertifikat">
                        @else
                            <div class="text-secondary mb-2">Belum ada latar. Sertifikat memakai latar putih.</div>
                        @endif
                        <input type="file" name="certificate_background" form="course-form"
                               class="form-control @error('certificate_background') is-invalid @enderror"
                               accept="image/jpeg,image/png">
                        <div class="form-hint">JPG atau PNG. Disarankan rasio 16:9 (mis. 1920×1080 px) atau A4 landscape (1123×794 px), maks 2 MB. Dipakai sebagai latar sertifikat.</div>
                        @error('certificate_background')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" form="course-form" class="btn btn-primary">Simpan perubahan</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-link">Batal</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Syarat kelulusan</h3>
                </div>
                <div class="card-body">
                    <p class="text-secondary small mb-3">
                        Biarkan kosong semua jika tidak ada syarat.
                    </p>

                    <form method="POST" action="{{ route('admin.courses.requirement.update', $course->uuid) }}" id="requirement-form">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="prerequisite_course_id">Kursus prasyarat</label>
                            <select id="prerequisite_course_id" name="prerequisite_course_id"
                                    class="form-select">
                                <option value="">Tidak ada prasyarat</option>
                                @php
                                    $otherCourses = \App\Domain\Catalog\Models\Course::withoutGlobalScope('organization')
                                        ->where('status', 'published')
                                        ->where('id', '!=', $course->id)
                                        ->orderBy('title')
                                        ->get();
                                @endphp                        @foreach ($otherCourses as $prereq)
                            <option value="{{ $prereq->id }}"
                                @selected(optional($course->requirement)?->prerequisite_course_id === $prereq->id)>
                                {{ $prereq->getTranslation('title', 'id') }}
                            </option>
                        @endforeach
                            </select>


                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="prerequisite_category_id">Kategori prasyarat</label>
                            <select id="prerequisite_category_id" name="prerequisite_category_id"
                                    class="form-select">
                                <option value="">Tidak ada kategori prasyarat</option>
                                @foreach (\App\Domain\Catalog\Models\Category::withoutGlobalScope('organization')
                                    ->orderBy('name')
                                    ->get() as $category)
                                <option value="{{ $category->id }}"
                                    @selected(optional($course->requirement)?->prerequisite_category_id === $category->id)">
                                    {{ $category->getTranslation('name', 'id') }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-hint">Peserta harus menyelesaikan semua kursus di kategori ini terlebih dahulu.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="min_grade">Nilai minimal kuis prasyarat</label>
                            <div class="input-group">
                                <input type="number" id="min_grade" name="min_grade"
                                       class="form-control"
                                       value="{{ optional($course->requirement)->min_grade }}"
                                       min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-hint">Diisi jika course prasyarat memiliki kuis yang harus lulus dengan nilai minimum.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="pendidikan_id">Pendidikan minimal peserta</label>
                            <select id="pendidikan_id" name="pendidikan_id" class="form-select">
                                <option value="">Tidak ada syarat pendidikan</option>
                                @foreach ($pendidikans as $pendidikan)
                                    <option value="{{ $pendidikan->id }}"
                                        @selected(optional($course->requirement)?->pendidikan_id === $pendidikan->id)>
                                        {{ $pendidikan->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">Peserta harus memiliki pendidikan setingkat ini atau lebih tinggi.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="min_age">Usia minimal peserta</label>
                                <div class="input-group">
                                    <input type="number" id="min_age" name="min_age"
                                           class="form-control"
                                           value="{{ optional($course->requirement)->min_age }}"
                                           min="1" max="150" placeholder="cth: 17">
                                    <span class="input-group-text">tahun</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="max_age">Usia maksimal peserta</label>
                                <div class="input-group">
                                    <input type="number" id="max_age" name="max_age"
                                           class="form-control"
                                           value="{{ optional($course->requirement)->max_age }}"
                                           min="1" max="150" placeholder="cth: 45">
                                    <span class="input-group-text">tahun</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-hint">Biarkan kosong jika tidak ada batasan usia.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="notes">Catatan syarat</label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="form-control">{{ optional($course->requirement)->notes }}</textarea>
                            <div class="form-hint">Catatan tambahan yang akan ditampilkan kepada peserta.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan syarat kelulusan</button>
                            @if ($course->requirement)
                                <button type="submit" class="btn btn-danger btn-sm" name="clear_requirement" value="1" onclick="return confirm('Hapus semua syarat kelulusan?')">Hapus semua syarat</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<x-hugerte :selectors="['#description_id']" />

@endsection