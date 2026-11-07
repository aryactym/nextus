@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Katalog</div>
            <h2 class="page-title">Kursus</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Tambah Kursus</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.courses.index') }}" class="row g-2 w-100">
            <div class="col-12 col-md-4">
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
                           placeholder="Cari judul atau slug" aria-label="Cari kursus">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="category" class="form-select" aria-label="Filter kategori">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters['category'] === (string) $category->id)>
                            {{ $category->getTranslation('name', 'id') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select" aria-label="Filter status">
                    <option value="">Semua status</option>
                    <option value="draft" @selected($filters['status'] === 'draft')>Draf</option>
                    <option value="published" @selected($filters['status'] === 'published')>Terbit</option>
                    <option value="archived" @selected($filters['status'] === 'archived')>Arsip</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="access_type" class="form-select" aria-label="Filter tipe akses">
                    <option value="">Semua akses</option>
                    <option value="free" @selected($filters['access_type'] === 'free')>Gratis</option>
                    <option value="donation" @selected($filters['access_type'] === 'donation')>Donasi</option>
                    <option value="paid" @selected($filters['access_type'] === 'paid')>Berbayar</option>
                </select>
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if (implode('', $filters) !== '')
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-link">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th class="w-1">#</th>
                    <th>Judul (ID)</th>
                    <th>Kategori</th>
                    <th>Akses</th>
                    <th>Sertifikat</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($courses as $course)
                @php
                    $accessLabels = ['free' => 'Gratis', 'donation' => 'Donasi', 'paid' => 'Berbayar'];
                    $statusLabels = ['published' => 'Terbit', 'draft' => 'Draf', 'archived' => 'Arsip'];
                    $statusBadges = ['published' => 'bg-green-lt', 'draft' => 'bg-secondary-lt', 'archived' => 'bg-orange-lt'];
                @endphp
                <tr>
                    <td class="text-secondary">{{ $courses->firstItem() + $loop->index }}</td>
                    <td>{{ $course->getTranslation('title', 'id') }}</td>
                    <td>{{ $course->category?->getTranslation('name', 'id') ?? '-' }}</td>
                    <td>{{ $accessLabels[$course->access_type] ?? $course->access_type }}</td>
                    <td>{{ $accessLabels[$course->certificate_type] ?? $course->certificate_type }}</td>
                    <td>
                        <span class="badge {{ $statusBadges[$course->status] ?? 'bg-secondary-lt' }}">
                            {{ $statusLabels[$course->status] ?? $course->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-list justify-content-end flex-nowrap">
                            <a href="{{ route('admin.courses.edit', $course->uuid) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                            <form action="{{ route('admin.courses.destroy', $course->uuid) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kursus ini?')">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary py-4">
                        Tidak ada kursus yang cocok dengan filter.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($courses->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $courses->withQueryString()->links('vendor.pagination.tabler') }}
        </div>
    @endif
</div>
@endsection

