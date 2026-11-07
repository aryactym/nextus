@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Katalog / Kategori</div>
            <h2 class="page-title">Tambah Kategori</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Informasi kategori</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name_id">Nama (Indonesia)</label>
                            <input type="text" id="name_id" name="name[id]"
                                   class="form-control @error('name.id') is-invalid @enderror"
                                   value="{{ old('name.id') }}" required>
                            @error('name.id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name_en">Nama (Inggris)</label>
                            <input type="text" id="name_en" name="name[en]"
                                   class="form-control @error('name.en') is-invalid @enderror"
                                   value="{{ old('name.en') }}">
                            @error('name.en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" id="slug" name="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}" required>
                        <div class="form-hint">Dipakai pada URL publik kategori, mis. <code>/kursus?kategori=nama-slug</code>.</div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="sort">Urutan tampil</label>
                        <input type="number" id="sort" name="sort" class="form-control"
                               value="{{ old('sort', 0) }}" min="0">
                        <div class="form-hint">Angka kecil tampil lebih dulu.</div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan kategori</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
