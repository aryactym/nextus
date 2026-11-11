@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Sistem / Organisasi</div>
            <h2 class="page-title">Tambah Organisasi</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.organizations.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="name">Nama Lembaga / Organisasi</label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="type">Tipe Organisasi</label>
                    <select id="type" name="type"
                            class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">-- Pilih tipe --</option>
                        @foreach ($types as $val => $label)
                            <option value="{{ $val }}" @selected(old('type') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status"
                            class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" @selected(old('status', 'active') === 'active')>Aktif</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Tidak aktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Organisasi</button>
                <a href="{{ route('admin.organizations.index') }}" class="btn btn-link">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
