@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Katalog / Kategori</div>
            <h2 class="page-title">Ubah Kategori</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('admin.categories.update', $category->uuid) }}">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Informasi kategori</h3>
                    <div class="card-actions">
                        <span class="badge bg-secondary-lt">{{ $category->getTranslation('name', 'id') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name_id">Nama (Indonesia)</label>
                            <input type="text" id="name_id" name="name[id]"
                                   class="form-control @error('name.id') is-invalid @enderror"
                                   value="{{ old('name.id', $category->getTranslation('name', 'id')) }}" required>
                            @error('name.id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name_en">Nama (Inggris)</label>
                            <input type="text" id="name_en" name="name[en]"
                                   class="form-control @error('name.en') is-invalid @enderror"
                                   value="{{ old('name.en', $category->getTranslation('name', 'en')) }}">
                            @error('name.en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" id="slug" name="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $category->slug) }}" required>
                        <div class="form-hint">Dipakai pada URL publik kategori.</div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="sort">Urutan tampil</label>
                        <input type="number" id="sort" name="sort" class="form-control"
                               value="{{ old('sort', $category->sort) }}" min="0">
                        <div class="form-hint">Angka kecil tampil lebih dulu.</div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Batal</a>
            </div>
        </form>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-secondary">Dibuat</dt>
                    <dd class="col-7">{{ $category->created_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                    <dt class="col-5 text-secondary">Diperbarui</dt>
                    <dd class="col-7 mb-0">{{ $category->updated_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
