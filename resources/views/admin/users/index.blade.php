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
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end w-100">
            {{-- Cari --}}
            <div class="col-md-3">
                <label class="form-label form-label-sm mb-1">Cari nama / email / jabatan</label>
                <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm"
                       placeholder="Ketik untuk mencari...">
            </div>

            {{-- Organisasi --}}
            <div class="col-md-3">
                <label class="form-label form-label-sm mb-1">Organisasi</label>
                <select name="org_id" id="org-select" class="form-select form-select-sm">
                    <option value="">Semua organisasi</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}" @selected($orgId === $org->id)>
                            {{ $org->getTranslation('name', 'id') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kategori jabatan --}}
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Kategori jabatan</label>
                <select name="kategori_jabatan" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach (['Officer', 'Supervisor', 'Manajer', 'General Manajer', 'Direktur', 'Lainnya'] as $kat)
                        <option value="{{ $kat }}" @selected($kategoriJabatan === $kat)>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Nama lembaga (free text) --}}
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Nama lembaga</label>
                <input type="search" name="nama_organisasi" value="{{ $namaOrganisasi }}"
                       class="form-control form-control-sm" placeholder="Filter lembaga...">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Terapkan</button>
                @if ($q !== '' || $kategoriJabatan !== '' || $namaOrganisasi !== '' || $orgId)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-link">Reset</a>
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
                    <th>Organisasi</th>
                    <th>Jabatan</th>
                    <th>Kategori</th>
                    <th>Peran</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="fw-medium">{{ $user->name }}</td>
                        <td class="text-secondary">{{ $user->email }}</td>
                        <td class="text-secondary small">
                            {{ $user->organization?->getTranslation('name', 'id') ?? '—' }}
                        </td>
                        <td class="small">
                            {{ $user->nama_jabatan ?? '—' }}
                            @if ($user->nama_organisasi)
                                <div class="text-muted" style="font-size:.78rem;">{{ $user->nama_organisasi }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($user->kategori_jabatan)
                                <span class="badge bg-azure-lt">{{ $user->kategori_jabatan }}</span>
                            @else
                                <span class="text-secondary">—</span>
                            @endif
                        </td>
                        <td>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-blue-lt">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-secondary">Ubah</a>
                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            Belum ada pengguna yang cocok dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
            dari {{ $users->total() }} pengguna
        </p>
        <div class="ms-auto">
            {{ $users->withQueryString()->links('vendor.pagination.tabler') }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/tom-select/dist/css/tom-select.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/tom-select/dist/js/tom-select.base.min.js"></script>
<script>
(function () {
    var el = document.getElementById('org-select');
    if (el) {
        new TomSelect(el, { allowEmptyOption: true, maxOptions: null });
    }
}());
</script>
@endpush
