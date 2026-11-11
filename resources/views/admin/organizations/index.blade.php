@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Sistem / Organisasi</div>
            <h2 class="page-title">Organisasi</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary">Tambah Organisasi</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.organizations.index') }}" class="d-flex gap-2 flex-wrap">
            <input type="search" name="q" value="{{ $q }}" class="form-control"
                   style="max-width: 280px;" placeholder="Cari nama organisasi">
            <select name="type" class="form-select" style="max-width: 180px;">
                <option value="">Semua tipe</option>
                @foreach ($types as $val => $label)
                    <option value="{{ $val }}" @selected($type === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th class="w-1">#</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Peserta</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($organizations as $organization)
                <tr>
                    <td class="text-secondary">{{ $organizations->firstItem() + $loop->index }}</td>
                    <td>{{ $organization->getTranslation('name', 'id') }}</td>
                    <td>
                        <span class="badge bg-blue-lt">{{ $types[$organization->type] ?? $organization->type }}</span>
                    </td>
                    <td>
                        @if ($organization->status === 'active')
                            <span class="badge bg-green-lt">Aktif</span>
                        @else
                            <span class="badge bg-gray-lt">Tidak aktif</span>
                        @endif
                    </td>
                    <td>{{ $organization->users_count }}</td>
                    <td class="text-end">
                        <div class="btn-list justify-content-end flex-nowrap">
                            <a href="{{ route('admin.organizations.edit', $organization) }}"
                               class="btn btn-sm btn-outline-primary">Ubah</a>
                            <form action="{{ route('admin.organizations.destroy', $organization) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Hapus organisasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        Tidak ada organisasi yang tersedia.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($organizations->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $organizations->links('vendor.pagination.tabler') }}
        </div>
    @endif
</div>
@endsection
