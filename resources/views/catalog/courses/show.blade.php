@extends('layouts.app')

@php
    $courseTitle = $course->getTranslation('title', 'id') ?: 'Detail kursus';
    $statusBadges = [
        'draft'     => 'bg-orange-lt',
        'published' => 'bg-green-lt',
        'archived'  => 'bg-gray-lt',
    ];
    $accessLabels = [
        'free'     => 'Gratis',
        'donation' => 'Donasi',
        'paid'     => 'Berbayar',
    ];
@endphp

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Katalog / Kursus</div>
            <h2 class="page-title">{{ $courseTitle }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none d-flex gap-2">
            @if ($course->status === 'published')
                <a href="{{ route('catalog.show', $course->slug) }}" target="_blank" rel="noopener"
                   class="btn btn-outline-secondary">Lihat halaman publik</a>
            @endif
            <a href="{{ route('admin.courses.edit', $course->uuid) }}" class="btn btn-primary">Ubah kursus</a>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

{{-- Stat cards --}}
<div class="row row-cards mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total peserta</div>
                <div class="h1 mt-1">{{ number_format($enrolledCount) }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Selesai</div>
                <div class="h1 mt-1">
                    {{ number_format($completedCount) }}
                    @if ($enrolledCount > 0)
                        <span class="text-muted fs-4">
                            ({{ round($completedCount / $enrolledCount * 100) }}%)
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Sertifikat diterbitkan</div>
                <div class="h1 mt-1">{{ number_format($certificateCount) }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total pendapatan</div>
                <div class="h1 mt-1">
                    @if ($revenue > 0)
                        Rp {{ number_format($revenue, 0, ',', '.') }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards">
    {{-- Kolom kiri: info kursus + tabel peserta --}}
    <div class="col-lg-8">

        {{-- Informasi kursus --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Informasi kursus</h3>
                <div class="card-actions">
                    <span class="badge {{ $statusBadges[$course->status] ?? 'bg-secondary-lt' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Kategori</dt>
                    <dd class="col-sm-8">{{ $course->category?->getTranslation('name', 'id') ?? '—' }}</dd>

                    <dt class="col-sm-4">Level</dt>
                    <dd class="col-sm-8">{{ $course->level ?? '—' }}</dd>

                    <dt class="col-sm-4">Tipe akses</dt>
                    <dd class="col-sm-8">{{ $accessLabels[$course->access_type] ?? ucfirst($course->access_type) }}</dd>

                    <dt class="col-sm-4">Harga minimal</dt>
                    <dd class="col-sm-8">
                        @if ($course->min_amount)
                            Rp {{ number_format($course->min_amount, 0, ',', '.') }}
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-sm-4">Durasi</dt>
                    <dd class="col-sm-8">
                        {{ $course->duration_minutes ? $course->duration_minutes.' menit' : '—' }}
                    </dd>

                    <dt class="col-sm-4">Modul / Pelajaran</dt>
                    <dd class="col-sm-8">{{ $moduleCount }} modul, {{ $lessonCount }} pelajaran</dd>

                    <dt class="col-sm-4">Passing grade</dt>
                    <dd class="col-sm-8">{{ $course->passing_grade }}%</dd>

                    <dt class="col-sm-4">Rata-rata progres</dt>
                    <dd class="col-sm-8">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px;">
                                <div class="progress-bar" style="width:{{ $avgProgress }}%"></div>
                            </div>
                            <span class="text-muted small">{{ $avgProgress }}%</span>
                        </div>
                    </dd>

                    <dt class="col-sm-4">Slug</dt>
                    <dd class="col-sm-8"><code>{{ $course->slug }}</code></dd>

                    @if ($course->published_at)
                        <dt class="col-sm-4">Diterbitkan</dt>
                        <dd class="col-sm-8">{{ $course->published_at->format('d M Y') }}</dd>
                    @endif
                </dl>

                @if ($course->getTranslation('summary', 'id'))
                    <hr>
                    <div class="text-muted small mb-1">Ringkasan</div>
                    <p class="mb-0">{{ $course->getTranslation('summary', 'id') }}</p>
                @endif
            </div>
        </div>

        {{-- Daftar peserta --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Peserta</h3>
                <div class="card-actions">
                    <span class="badge bg-secondary-lt">{{ $enrolledCount }} peserta</span>
                </div>
            </div>

            @if ($course->enrollments->isEmpty())
                <div class="card-body text-center text-muted py-5">
                    Belum ada peserta yang mendaftar.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Peserta</th>
                                <th>Progres</th>
                                <th>Mendaftar</th>
                                <th>Selesai</th>
                                <th>Sertifikat</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($course->enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $enrollment->user?->name ?? '—' }}</div>
                                        <div class="text-muted small">{{ $enrollment->user?->email ?? '' }}</div>
                                    </td>
                                    <td style="min-width:130px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px;">
                                                <div class="progress-bar" style="width:{{ $enrollment->progress_percent }}%"></div>
                                            </div>
                                            <span class="text-muted small">{{ $enrollment->progress_percent }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $enrollment->enrolled_at?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="text-muted small">
                                        @if ($enrollment->completed_at)
                                            <span class="badge bg-green-lt">
                                                {{ $enrollment->completed_at->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($enrollment->certificate)
                                            @if ($enrollment->certificate->isRevoked())
                                                <span class="badge bg-red-lt">Dicabut</span>
                                            @else
                                                <span class="badge bg-green-lt">Diterbitkan</span>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($enrollment->order)
                                            <a href="{{ route('admin.payments.show', $enrollment->order->uuid) }}"
                                               class="btn btn-sm btn-outline-secondary">Pembayaran</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    {{-- Kolom kanan: sidebar --}}
    <div class="col-lg-4">

        {{-- Modul & pelajaran --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Modul &amp; pelajaran</h3>
                <div class="card-actions">
                    <span class="badge bg-secondary-lt">{{ $moduleCount }} modul</span>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse ($course->modules as $module)
                    <div class="px-3 py-2 border-bottom">
                        <div class="fw-medium">{{ $module->getTranslation('title', 'id') }}</div>
                        <div class="text-muted small">{{ $module->lessons->count() }} pelajaran</div>
                    </div>
                @empty
                    <div class="px-3 py-4 text-muted text-center">Belum ada modul.</div>
                @endforelse
            </div>
        </div>

        {{-- Pendapatan per bulan jika berbayar --}}
        @if ($course->access_type !== 'free' && $revenue > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Ringkasan pendapatan</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-7">Total pendapatan</dt>
                        <dd class="col-5 text-end fw-medium">Rp {{ number_format($revenue, 0, ',', '.') }}</dd>

                        <dt class="col-7">Transaksi sukses</dt>
                        <dd class="col-5 text-end">
                            {{ $course->enrollments->filter(fn ($e) => $e->order && $e->order->status === 'paid')->count() }}
                        </dd>
                    </dl>
                </div>
            </div>
        @endif

        {{-- Tautan cepat --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tautan cepat</h3>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.courses.edit', $course->uuid) }}"
                   class="list-group-item list-group-item-action">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-2" width="18" height="18"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                        </svg>
                        Ubah kursus
                    </div>
                </a>

                @if ($course->status === 'published')
                    <a href="{{ route('catalog.show', $course->slug) }}" target="_blank" rel="noopener"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-2" width="18" height="18"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M11 7h-5a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-5"/>
                                <path d="M10 14l10 -10"/>
                                <path d="M15 4l5 0l0 5"/>
                            </svg>
                            Lihat halaman publik
                        </div>
                    </a>
                @endif

                <a href="{{ route('admin.payments.index', ['course' => $course->id]) }}"
                   class="list-group-item list-group-item-action">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-2" width="18" height="18"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <rect x="3" y="5" width="18" height="14" rx="3"/>
                            <path d="M3 10h18"/>
                            <path d="M7 15h.01"/>
                            <path d="M11 15h2"/>
                        </svg>
                        Lihat pembayaran
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
