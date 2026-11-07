@extends('layouts.app')

@php
    $stats = [
        [
            'label' => 'Kursus',
            'value' => \App\Domain\Catalog\Models\Course::forOrganization()->count(),
            'route' => route('admin.courses.index'),
            'color' => 'green',
            'icon' => '<path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/>',
        ],
        [
            'label' => 'Kategori',
            'value' => \App\Domain\Catalog\Models\Category::forOrganization()->count(),
            'route' => route('admin.categories.index'),
            'color' => 'blue',
            'icon' => '<path d="M4 4h6v6h-6z"/><path d="M14 4h6v6h-6z"/><path d="M4 14h6v6h-6z"/><path d="M14 14h6v6h-6z"/>',
        ],
        [
            'label' => 'Halaman',
            'value' => \App\Domain\Admin\Models\Page::query()->count(),
            'route' => route('admin.pages.index'),
            'color' => 'orange',
            'icon' => '<path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>',
        ],
    ];
@endphp

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Ringkasan</div>
            <h2 class="page-title">Dashboard Admin</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Tambah kursus</a>
        </div>
    </div>
</div>

<div class="row row-deck row-cards mb-3">
    @foreach ($stats as $stat)
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="subheader">{{ $stat['label'] }}</div>
                            <div class="h1 mb-0 mt-2">{{ $stat['value'] }}</div>
                        </div>
                        <span class="bg-{{ $stat['color'] }}-lt avatar avatar-lg rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="icon icon-tabler">
                                {!! $stat['icon'] !!}
                            </svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        <a href="{{ $stat['route'] }}" class="link-secondary">Kelola {{ strtolower($stat['label']) }} &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Aksi cepat</h3>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('admin.courses.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span>Kelola kursus dan modul</span>
            <span class="text-secondary">&rarr;</span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span>Atur kategori kursus</span>
            <span class="text-secondary">&rarr;</span>
        </a>
        <a href="{{ route('admin.pages.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span>Ubah halaman Syarat &amp; Privasi</span>
            <span class="text-secondary">&rarr;</span>
        </a>
        @can('manage settings')
            <a href="{{ route('admin.settings.edit') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span>Pengaturan pelacakan &amp; SMTP</span>
                <span class="text-secondary">&rarr;</span>
            </a>
        @endcan
    </div>
</div>
@endsection

