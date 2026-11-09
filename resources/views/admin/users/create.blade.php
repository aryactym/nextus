@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Sistem / Pengguna</div>
            <h2 class="page-title">Tambah Pengguna</h2>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Informasi akun</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="role">Peran</label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Pilih peran</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role') === $role->name)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pendidikan_id">Pendidikan terakhir</label>
                        <select id="pendidikan_id" name="pendidikan_id"
                                class="form-select @error('pendidikan_id') is-invalid @enderror">
                            <option value="">Pilih pendidikan</option>
                            @foreach ($pendidikans as $p)
                                <option value="{{ $p->id }}" @selected(old('pendidikan_id') === (string) $p->id)>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pendidikan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="nama_jabatan">Nama jabatan</label>
                        <input type="text" id="nama_jabatan" name="nama_jabatan"
                               class="form-control @error('nama_jabatan') is-invalid @enderror"
                               placeholder="Misal: Manajer Program"
                               value="{{ old('nama_jabatan') }}">
                        @error('nama_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="kategori_jabatan">Kategori jabatan</label>
                        <select id="kategori_jabatan" name="kategori_jabatan"
                                class="form-select @error('kategori_jabatan') is-invalid @enderror">
                            <option value="">Pilih kategori</option>
                            @foreach (['Officer', 'Supervisor', 'Manajer', 'General Manajer', 'Direktur', 'Lainnya'] as $kategori)
                                <option value="{{ $kategori }}" @selected(old('kategori_jabatan') === $kategori)>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="nama_organisasi">Nama organisasi</label>
                        <input type="text" id="nama_organisasi" name="nama_organisasi"
                               class="form-control @error('nama_organisasi') is-invalid @enderror"
                               placeholder="Misal: BAZNAS, Yayasan XYZ"
                               value="{{ old('nama_organisasi') }}">
                        @error('nama_organisasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan pengguna</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-link">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection

