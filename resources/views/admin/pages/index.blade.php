@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Konten</div>
            <h2 class="page-title">Halaman</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.pages.index') }}" class="row g-2 w-100">
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
                           placeholder="Cari judul atau slug" aria-label="Cari halaman">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select" aria-label="Filter status">
                    <option value="">Semua status</option>
                    <option value="published" @selected($filters['status'] === 'published')>Terbit</option>
                    <option value="draft" @selected($filters['status'] === 'draft')>Draf</option>
                </select>
            </div>
            <div class="col-6 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($filters['q'] !== '' || $filters['status'] !== '')
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-link">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th class="w-1">#</th>
                    <th>Judul</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Terakhir diperbarui</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($pages as $page)
                <tr>
                    <td class="text-secondary">{{ $pages->firstItem() + $loop->index }}</td>
                    <td>{{ $page->title }}</td>
                    <td><code class="text-secondary">{{ $page->slug }}</code></td>
                    <td>
                        @if ($page->is_published)
                            <span class="badge bg-green-lt">Terbit</span>
                        @else
                            <span class="badge bg-secondary-lt">Draf</span>
                        @endif
                    </td>
                    <td class="text-secondary">{{ $page->updated_at?->format('d M Y H:i') ?? '-' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.pages.edit', $page->uuid) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        Tidak ada halaman yang cocok dengan filter.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($pages->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $pages->links('vendor.pagination.tabler') }}
        </div>
    @endif
</div>
@endsection
