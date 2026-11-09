@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Halaman statis</div>
            <h2 class="page-title">Ubah Halaman</h2>
        </div>
        <div class="col-auto ms-auto d-print-none d-flex gap-2">
            <a
                href="{{ $page->slug === 'syarat' ? route('syarat') : route('privasi') }}"
                class="btn btn-outline-secondary"
                target="_blank"
                rel="noopener"
            >Lihat halaman</a>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.pages.update', $page->uuid) }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-9">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Konten halaman</h3>
                    <div class="card-actions">
                        @if ($page->is_published)
                            <span class="badge bg-green-lt">Terbit</span>
                        @else
                            <span class="badge bg-secondary-lt">Draf</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label class="form-label" for="title">Judul halaman</label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $page->title) }}"
                                required
                            >
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="slug">Slug</label>
                            <input type="text" id="slug" class="form-control" value="{{ $page->slug }}" disabled>
                            <div class="form-hint">Tidak dapat diubah.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="meta_description">Deskripsi singkat</label>
                        <input
                            type="text"
                            id="meta_description"
                            name="meta_description"
                            class="form-control @error('meta_description') is-invalid @enderror"
                            value="{{ old('meta_description', $page->meta_description) }}"
                        >
                        <div class="form-hint">Tampil di bawah judul halaman dan menjadi meta description.</div>
                        @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="page-content">Isi halaman</label>
                        <textarea
                            id="page-content"
                            name="content"
                            rows="24"
                            class="form-control @error('content') is-invalid @enderror"
                        >{{ old('content', $page->content) }}</textarea>
                        <div class="form-hint">
                            Gunakan tombol format untuk teks. Judul bagian memakai format Heading 2 agar otomatis masuk daftar isi.
                        </div>
                        @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-3">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-link">Batal</a>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Publikasi</h3>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input
                            type="checkbox"
                            id="is_published"
                            name="is_published"
                            value="1"
                            class="form-check-input"
                            {{ old('is_published', $page->is_published) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="is_published">Terbitkan halaman</label>
                    </div>

                    <dl class="row mb-0">
                        <dt class="col-5 text-secondary">Diperbarui</dt>
                        <dd class="col-7 mb-0">{{ $page->updated_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</form>

<x-hugerte :selectors="['#page-content']" :height="600" />

@endsection

