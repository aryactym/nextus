@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Sistem / Sponsor</div>
            <h2 class="page-title">Mitra &amp; Sponsor</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.sponsors.create') }}" class="btn btn-primary">Tambah Sponsor</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.sponsors.index') }}" class="d-flex gap-2">
            <input type="search" name="q" value="{{ $q ?? '' }}" class="form-control"
                   placeholder="Cari nama sponsor">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th class="w-1">#</th>
                    <th>Nama</th>
                    <th>Logo</th>
                    <th>URL</th>
                    <th>Urutan</th>
                    <th class="w-1 text-center">Aktif</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($sponsors as $sponsor)
                <tr>
                    <td class="text-secondary">{{ $sponsors->firstItem() + $loop->index }}</td>
                    <td>{{ $sponsor->name }}</td>
                    <td>
                        @if ($sponsor->logo)
                            <img src="{{ Storage::disk('public')->url($sponsor->logo) }}"
                                 class="img-fluid rounded border" alt="{{ $sponsor->name }}"
                                 style="max-height: 40px;">
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($sponsor->url)
                            <a href="{{ $sponsor->url }}" target="_blank" rel="noopener"
                               class="text-decoration-none">{{ Str::limit($sponsor->url, 40) }}</a>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>
                    <td>{{ $sponsor->sort }}</td>
                    <td class="text-center">
                        @if ($sponsor->is_active)
                            <span class="badge bg-green-lt">Aktif</span>
                        @else
                            <span class="badge bg-gray-lt">Tidak aktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-list justify-content-end flex-nowrap">
                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}"
                               class="btn btn-sm btn-outline-primary">Ubah</a>
                            <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Hapus sponsor ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary py-4">
                        Tidak ada sponsor yang tersedia.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($sponsors->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $sponsors->links('vendor.pagination.tabler') }}
        </div>
    @endif
</div>
@endsection
