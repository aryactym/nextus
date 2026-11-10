@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Katalog</div>
            <h2 class="page-title">Detail Kategori: {{ $category->getTranslation('name', 'id') }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
        </div>
    </div>
</div>

@if ($category)
<div class="card mt-4">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0">Informasi Kategori</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.categories.edit', $category->uuid) }}" class="btn btn-sm btn-outline-primary">Ubah Kategori</a>
                @can('manage categories')
                    <form action="{{ route('admin.categories.destroy', $category->uuid) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin menghapus kategori ini?')">Hapus</button>
                    </form>
                @endcan>
            </div>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Nama</dt>
            <dd class="col-sm-9">{{ $category->getTranslation('name', 'id') }}</dd>

            <dt class="col-sm-3">Nama (EN)</dt>
            <dd class="col-sm-9">{{ $category->getTranslation('name', 'en') ?: '-' }}</dd>

            <dt class="col-sm-3">Slug</dt>
            <dd class="col-sm-9"><code class="text-secondary">{{ $category->slug }}</code></dd>

            <dt class="col-sm-3">Urutan</dt>
            <dd class="col-sm-9">{{ $category->sort }}</dd>

            <dt class="col-sm-3">Jumlah Kursus</dt>
            <dd class="col-sm-9">{{ $coursesInCategory->count() }}</dd>
        </dl>
    </div>
</div>
@endif

@if ($categories)
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Kursus di Kategori Ini</h5>
    </div>
    <div class="card-body">
        @if ($coursesInCategory->isEmpty())
            <div class="alert alert-info">
                Tidak ada kursus terbit di kategori ini.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Judul</th>
                            <th>Level</th>
                            <th>Tipe Akses</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($coursesInCategory as $course)
                        <tr>
                            <td class="text-secondary">{{ $loop->index }}</td>
                            <td>
                                <a href="{{ route('catalog.show', $course->slug) }}" target="_blank">
                                    {{ $course->getTranslation('title', 'id') }}
                                </a>
                            </td>
                            <td>{{ $course->level ?: '-' }}</td>
                            <td>
                                @if ($course->access_type === 'free')
                                    <span class="badge bg-success">Gratis</span>
                                @elseif ($course->access_type === 'donation')
                                    <span class="badge bg-info">Donasi</span>
                                @else
                                    <span class="badge bg-primary">Bayar</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.courses.show', $course->uuid) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                <a href="{{ route('admin.courses.edit', $course->uuid) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endif