@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Sistem / Sponsor</div>
            <h2 class="page-title">Ubah Sponsor</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.sponsors.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.sponsors.update', $sponsor) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label" for="name">Nama Sponsor</label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $sponsor->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="url">URL Situs (opsional)</label>
                <input type="url" id="url" name="url"
                       class="form-control @error('url') is-invalid @enderror"
                       value="{{ old('url', $sponsor->url) }}" placeholder="https://contoh.com">
                @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="logo">Logo Sponsor</label>
                @if ($sponsor->logo)
                    <div class="mb-2">
                        <img src="{{ Storage::disk('public')->url($sponsor->logo) }}"
                             class="img-fluid rounded border" alt="{{ $sponsor->name }}"
                             style="max-height: 60px;">
                    </div>
                @endif
                <input type="file" id="logo" name="logo"
                       class="form-control @error('logo') is-invalid @enderror"
                       accept="image/*">
                <div class="form-hint">JPG, PNG, atau WebP. Maksimal 2 MB. Kosongkan jika tidak diubah.</div>
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="sort">Urutan Tampilan</label>
                    <input type="number" id="sort" name="sort"
                           class="form-control @error('sort') is-invalid @enderror"
                           value="{{ old('sort', $sponsor->sort) }}" min="0">
                    @error('sort')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               class="form-check-input" {{ $sponsor->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Aktif di halaman utama
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Sponsor</button>
                <a href="{{ route('admin.sponsors.index') }}" class="btn btn-link">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
