@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi / Peserta</div>
            <h2 class="page-title">{{ $learner->name }}</h2>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <a href="{{ route('admin.org.learners.edit', $learner->uuid) }}" class="btn btn-primary">Ubah</a>
            <a href="{{ route('admin.org.learners.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

@php
    $completedEnrollments = $learner->enrollments->whereNotNull('completed_at');
    $activeEnrollments    = $learner->enrollments->whereNull('completed_at');
    $certCount            = $learner->enrollments->filter(fn ($e) => $e->certificate)->count();
    $avgProgress          = $learner->enrollments->count()
        ? (int) round($learner->enrollments->avg('progress_percent'))
        : 0;
@endphp

{{-- Stat cards --}}
<div class="row row-cards mb-3">
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Kursus diikuti</div>
                <div class="h1 mt-1">{{ $learner->enrollments->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Selesai</div>
                <div class="h1 mt-1">{{ $completedEnrollments->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Sertifikat</div>
                <div class="h1 mt-1">{{ $certCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Rata-rata progres</div>
                <div class="h1 mt-1">{{ $avgProgress }}%</div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards">
    <div class="col-lg-8">

        {{-- Daftar kursus --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kursus yang diikuti</h3>
            </div>
            @if ($learner->enrollments->isEmpty())
                <div class="card-body text-center text-secondary py-4">
                    Belum mengikuti kursus apapun.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Kursus</th>
                                <th>Topik</th>
                                <th>Progres</th>
                                <th>Selesai</th>
                                <th>Sertifikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($learner->enrollments as $enrollment)
                                <tr>
                                    <td class="fw-medium">
                                        {{ $enrollment->course?->getTranslation('title', 'id') ?? '—' }}
                                    </td>
                                    <td class="text-secondary small">
                                        {{ $enrollment->course?->category?->getTranslation('name', 'id') ?? '—' }}
                                    </td>
                                    <td style="min-width:120px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px;">
                                                <div class="progress-bar"
                                                     style="width:{{ $enrollment->progress_percent }}%"></div>
                                            </div>
                                            <span class="text-muted small">{{ $enrollment->progress_percent }}%</span>
                                        </div>
                                    </td>
                                    <td class="small">
                                        @if ($enrollment->completed_at)
                                            <span class="badge bg-green-lt">
                                                {{ $enrollment->completed_at->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">Belum</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($enrollment->certificate)
                                            <span class="badge bg-green-lt">Ada</span>
                                        @else
                                            <span class="text-muted">—</span>
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

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profil peserta</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-secondary">Email</dt>
                    <dd class="col-7">{{ $learner->email }}</dd>

                    <dt class="col-5 text-secondary">Jabatan</dt>
                    <dd class="col-7">{{ $learner->nama_jabatan ?? '—' }}</dd>

                    <dt class="col-5 text-secondary">Kategori</dt>
                    <dd class="col-7">
                        @if ($learner->kategori_jabatan)
                            <span class="badge bg-azure-lt">{{ $learner->kategori_jabatan }}</span>
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-5 text-secondary">Unit kerja</dt>
                    <dd class="col-7">{{ $learner->nama_organisasi ?? '—' }}</dd>

                    <dt class="col-5 text-secondary">Pendidikan</dt>
                    <dd class="col-7">{{ $learner->pendidikan?->name ?? '—' }}</dd>

                    <dt class="col-5 text-secondary">Bergabung</dt>
                    <dd class="col-7">{{ $learner->created_at?->format('d M Y') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
