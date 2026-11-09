@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Sistem</div>
            <h2 class="page-title">Pengguna</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tambah pengguna</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label form-label-sm mb-1">Cari nama / email / jabatan / organisasi</label>
                <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Ketik untuk mencari...">
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm mb-1">Kategori jabatan</label>
                <select name="kategori_jabatan" class="form-select form-select-sm">
                    <option value="">Semua kategori</option>
                    @foreach (['Officer', 'Supervisor', 'Manajer', 'General Manajer', 'Direktur', 'Lainnya'] as $kategori)
                        <option value="{{ $kategori }}" @selected($kategoriJabatan === $kategori)>{{ $kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm mb-1">Nama organisasi</label>
                <input type="search" name="nama_organisasi" value="{{ $namaOrganisasi }}" class="form-control form-control-sm" placeholder="Filter organisasi...">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-outline-secondary flex-fill">Filter</button>
                @if ($q !== '' || $kategoriJabatan !== '' || $namaOrganisasi !== '')
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-ghost-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jabatan</th>
                    <th>Kategori</th>
                    <th>Organisasi</th>
                    <th>Peran</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->nama_jabatan ?? '—' }}</td>
                        <td>
                            @if ($user->kategori_jabatan)
                                <span class="badge bg-azure-lt">{{ $user->kategori_jabatan }}</span>
                            @else
                                <span class="text-secondary">—</span>
                            @endif
                        </td>
                        <td>{{ $user->nama_organisasi ?? '—' }}</td>
                        <td>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-blue-lt">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Ubah</a>
                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Menampilkan {{ $users->firstItem() ?? 0 }} sampai {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
        </p>
        <ul class="pagination m-0 ms-auto">
            {{ $users->withQueryString()->links('vendor.pagination.tabler') }}
        </ul>
    </div>
</div>
@endsection
