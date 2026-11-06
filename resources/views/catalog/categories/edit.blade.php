@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Ubah Kategori</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.update', $category->uuid) }}">
                    @csrf @method('PUT')
                    <div class="form-group mb-3">
                        <label>Nama (Indonesia)</label>
                        <input type="text" name="name[id]" class="form-control @error('name.id') is-invalid @enderror"
                               value="{{ old('name.id', $category->name['id'] ?? '') }}" required>
                        @error('name.id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama (Inggris)</label>
                        <input type="text" name="name[en]" class="form-control @error('name.en') is-invalid @enderror"
                               value="{{ old('name.en', $category->name['en'] ?? '') }}">
                        @error('name.en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $category->slug) }}" required>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Urutan</label>
                        <input type="number" name="sort" class="form-control" value="{{ old('sort', $category->sort) }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ms-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
