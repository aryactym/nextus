@extends('layouts.app')

@php
    $accessOptions = ['free' => 'Gratis', 'donation' => 'Donasi', 'paid' => 'Berbayar'];
@endphp

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Katalog / Kursus</div>
            <h2 class="page-title">Tambah Kursus</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
    @csrf

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
                                   value="{{ old('title.id') }}" required>
                            @error('title.id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title_en">Judul (Inggris)</label>
                            <input type="text" id="title_en" name="title[en]"
                                   class="form-control @error('title.en') is-invalid @enderror"
                                   value="{{ old('title.en') }}">
                            @error('title.en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" id="slug" name="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}" required>
                        <div class="form-hint">Dipakai pada URL publik, mis. <code>/kursus/nama-kursus</code>.</div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="category_id">Kategori</label>
                            <select id="category_id" name="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Tanpa kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        @selected((string) old('category_id') === (string) $category->id)>
                                        {{ $category->getTranslation('name', 'id') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="level">Level</label>
                            <input type="text" id="level" name="level"
                                   class="form-control @error('level') is-invalid @enderror"
                                   value="{{ old('level', 'beginner') }}" required>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="duration_minutes">Durasi (menit)</label>
                            <input type="number" id="duration_minutes" name="duration_minutes"
                                   class="form-control @error('duration_minutes') is-invalid @enderror"
                                   value="{{ old('duration_minutes') }}" min="0">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="summary_id">Ringkasan (Indonesia)</label>
                        <textarea id="summary_id" name="summary[id]" rows="2"
                                  class="form-control @error('summary.id') is-invalid @enderror">{{ old('summary.id') }}</textarea>
                        <div class="form-hint">Tampil singkat di kartu katalog.</div>
                        @error('summary.id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="description_id">Deskripsi (Indonesia)</label>
                        <textarea id="description_id" name="description[id]" rows="6"
                                  class="form-control @error('description.id') is-invalid @enderror">{{ old('description.id') }}</textarea>
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
                            <option value="draft" @selected(old('status', 'draft') === 'draft')>Draf</option>
                            <option value="published" @selected(old('status') === 'published')>Terbit</option>
                            <option value="archived" @selected(old('status') === 'archived')>Arsip</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="access_type">Tipe akses</label>
                        <select id="access_type" name="access_type" class="form-select @error('access_type') is-invalid @enderror" required>
                            @foreach ($accessOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('access_type', 'free') === $value)>{{ $label }}</option>
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
                                <option value="{{ $value }}" @selected(old('certificate_type', 'free') === $value)>{{ $label }}</option>
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
                                   value="{{ old('passing_grade', 75) }}" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                        @error('passing_grade')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label" for="thumbnail">Thumbnail</label>
                        <input type="file" id="thumbnail" name="thumbnail"
                               class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                        <div class="form-hint">JPG, PNG, atau WebP. Maksimal 2 MB.</div>
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan kursus</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-link">Batal</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Catatan</h3>
                </div>
                <div class="card-body text-secondary">
                    <p class="mb-0">
                        Setelah kursus dibuat, tambahkan modul dan pelajaran dari halaman ubah kursus.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

<x-hugerte :selectors="['#description_id']" />

@endsection

