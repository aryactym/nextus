@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Blog</div>
            <h2 class="page-title">Artikel</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="icon" aria-hidden="true"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                Tulis artikel
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="row g-2 w-100">
            <div class="col-12 col-md-7">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="icon" aria-hidden="true">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                            <path d="M21 21l-6 -6"/>
                        </svg>
                    </span>
                    <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Cari judul atau slug">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    <option value="draft" @selected($status === 'draft')>Draf</option>
                    <option value="published" @selected($status === 'published')>Terbit</option>
                </select>
            </div>
            <div class="col-6 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($q !== '' || $status !== '')
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-link">Reset</a>
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
                    <th>Penulis</th>
                    <th>Status</th>
                    <th>Diterbitkan</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td class="text-secondary">{{ $posts->firstItem() + $loop->index }}</td>
                    <td>
                        <a href="{{ route('admin.posts.edit', $post->uuid) }}" class="fw-medium text-reset">
                            {{ $post->title }}
                        </a>
                        <div class="small text-muted">{{ $post->slug }}</div>
                    </td>
                    <td class="text-secondary">{{ $post->author?->name ?? '-' }}</td>
                    <td>
                        @if ($post->status === 'published')
                            <span class="badge bg-green-lt">Terbit</span>
                        @else
                            <span class="badge bg-orange-lt">Draf</span>
                        @endif
                    </td>
                    <td class="text-secondary small">
                        {{ $post->published_at?->format('d M Y') ?? '-' }}
                    </td>
                    <td class="text-end">
                        <div class="btn-list justify-content-end flex-nowrap">
                            @if ($post->status === 'published')
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener"
                                   class="btn btn-sm btn-outline-secondary">Lihat</a>
                            @endif
                            <a href="{{ route('admin.posts.edit', $post->uuid) }}"
                               class="btn btn-sm btn-outline-primary">Ubah</a>
                            <form action="{{ route('admin.posts.destroy', $post->uuid) }}" method="POST"
                                  onsubmit="return confirm('Hapus artikel ini?')" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        Belum ada artikel.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($posts->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $posts->withQueryString()->links('vendor.pagination.tabler') }}
        </div>
    @endif
</div>
@endsection
