@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Tambah Kursus</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Judul (Indonesia)</label>
                            <input type="text" name="title[id]" class="form-control @error('title.id') is-invalid @enderror"
                                   value="{{ old('title.id') }}" required>
                            @error('title.id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Judul (Inggris)</label>
                            <input type="text" name="title[en]" class="form-control @error('title.en') is-invalid @enderror"
                                   value="{{ old('title.en') }}">
                            @error('title.en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}" required>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Tanpa kategori</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name['id'] ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tipe Akses</label>
                            <select name="access_type" class="form-select">
                                <option value="free">Gratis</option>
                                <option value="donation">Donasi</option>
                                <option value="paid">Berbayar</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tipe Sertifikat</label>
                            <select name="certificate_type" class="form-select">
                                <option value="free">Gratis</option>
                                <option value="donation">Donasi</option>
                                <option value="paid">Berbayar</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Level</label>
                            <input type="text" name="level" class="form-control" value="{{ old('level', 'beginner') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Passing Grade</label>
                            <input type="number" name="passing_grade" class="form-control" value="{{ old('passing_grade', 75) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        <p class="form-text text-muted">Maks 2 MB (jpg, png, webp).</p>
                    </div>
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="published">Publish</option>
                            <option value="archived">Arsip</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Kursus</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary ms-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
