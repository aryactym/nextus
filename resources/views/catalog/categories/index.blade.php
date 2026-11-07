@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Katalog</div>
            <h2 class="page-title">Kategori</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 w-100">
            <div class="col-12 col-md-5">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="icon" aria-hidden="true">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                            <path d="M21 21l-6 -6"/>
                        </svg>
                    </span>
                    <input type="search" name="q" value="{{ $filters['q'] }}" class="form-control"
                           placeholder="Cari nama atau slug" aria-label="Cari kategori">
                </div>
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($filters['q'] !== '')
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th class="w-1">#</th>
                    <th>Nama (ID)</th>
                    <th>Nama (EN)</th>
                    <th>Slug</th>
                    <th>Urutan</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td class="text-secondary">{{ $categories->firstItem() + $loop->index }}</td>
                    <td>{{ $category->getTranslation('name', 'id') }}</td>
                    <td class="text-secondary">{{ $category->getTranslation('name', 'en') ?: '-' }}</td>
                    <td><code class="text-secondary">{{ $category->slug }}</code></td>
                    <td>{{ $category->sort }}</td>
                    <td class="text-end">
                        <div class="btn-list justify-content-end flex-nowrap">
                            <a href="{{ route('admin.categories.edit', $category->uuid) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                            <form action="{{ route('admin.categories.destroy', $category->uuid) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        Tidak ada kategori yang cocok dengan filter.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($categories->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $categories->links('vendor.pagination.tabler') }}
        </div>
    @endif
</div>
@endsection

