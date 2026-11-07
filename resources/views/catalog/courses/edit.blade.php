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
            <a href="{{ route('admin.courses.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.courses.update', $course->uuid) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Informasi kursus</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title_id">Judul (Indonesia)</label>
                            <input type="text" id="title_id" name="title[id]"
                                   class="form-control @error('title.id') is-invalid @enderror"
                                   value="{{ old('title.id', $course->getTranslation('title', 'id')) }}" required>
                            @error('title.id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
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

                    <div class="row">
                        <div class="col-md-5 mb-3">
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
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="level">Level</label>
                            <input type="text" id="level" name="level"
                                   class="form-control @error('level') is-invalid @enderror"
                                   value="{{ old('level', $course->level) }}" placeholder="beginner" required>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
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
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Publikasi</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
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
                        <select id="access_type" name="access_type" class="form-select @error('access_type') is-invalid @enderror" required>
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
                        <select id="certificate_type" name="certificate_type" class="form-select @error('certificate_type') is-invalid @enderror" required>
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
                            <input type="number" id="passing_grade" name="passing_grade"
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
                            <img src="{{ route('admin.uploads.show', ['path' => $course->thumbnail]) }}"
                                 class="img-fluid rounded border mb-2" alt="Thumbnail kursus">
                        @else
                            <div class="text-secondary mb-2">Belum ada thumbnail.</div>
                        @endif
                        <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                        <div class="form-hint">JPG, PNG, atau WebP. Maksimal 2 MB.</div>
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-link">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-12">
        <div class="card">
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
                                <h4 class="mb-1">{{ $module->getTranslation('title', 'id') }}</h4>
                                <div class="text-secondary small">{{ $module->lessons->count() }} pelajaran</div>
                            </div>
                            <form action="{{ route('admin.modules.destroy', $module) }}" method="POST"
                                  onsubmit="return confirm('Hapus modul ini beserta pelajarannya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus modul</button>
                            </form>
                        </div>

                        @if ($module->lessons->isNotEmpty())
                            <div class="list-group list-group-flush my-3">
                                @foreach ($module->lessons as $lesson)
                                    <div class="list-group-item d-flex justify-content-between align-items-center gap-2 px-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $lessonBadges[$lesson->type] ?? 'bg-secondary-lt' }}">
                                                {{ ucfirst($lesson->type) }}
                                            </span>
                                            <span>{{ $lesson->getTranslation('title', 'id') }}</span>
                                        </div>
                                        <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST"
                                              onsubmit="return confirm('Hapus pelajaran ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <a class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse"
                           href="#lesson-form-{{ $module->uuid }}" role="button"
                           aria-expanded="false" aria-controls="lesson-form-{{ $module->uuid }}">
                            Tambah pelajaran
                        </a>

                        <div class="collapse mt-3" id="lesson-form-{{ $module->uuid }}">
                            <form action="{{ route('admin.modules.lessons.store', $module) }}" method="POST" enctype="multipart/form-data">
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
                                    <div class="col-md-6">
                                        <label class="form-label">Konten (Indonesia)</label>
                                        <textarea name="content[id]" class="form-control" rows="2" placeholder="Konten pelajaran"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Konten (Inggris)</label>
                                        <textarea name="content[en]" class="form-control" rows="2" placeholder="Lesson content"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">URL video</label>
                                        <input type="url" name="video_url" class="form-control" placeholder="https://...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">File PDF</label>
                                        <input type="file" name="file" class="form-control" accept="application/pdf">
                                    </div>
                                    <div class="col-md-2">
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
                    <div class="col-md-9">
                        <label class="form-label visually-hidden" for="new_module_title">Judul modul baru</label>
                        <input type="text" id="new_module_title" name="title[id]" class="form-control"
                               placeholder="Judul modul baru" required>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Tambah modul</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
