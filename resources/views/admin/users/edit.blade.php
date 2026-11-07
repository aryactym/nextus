@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Sistem / Pengguna</div>
            <h2 class="page-title">Ubah Pengguna</h2>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf
    @method('PUT')

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
                               value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="role">Peran</label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}"
                                    @selected(old('role', $user->roles->first()?->name) === $role->name)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Password baru (opsional)</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror">
                        <div class="form-hint">Kosongkan jika tidak ingin mengubah password.</div>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-link">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
