@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi</div>
            <h2 class="page-title">Laporan Capaian</h2>
        </div>
    </div>
</div>

@include('org.reports._tabs')
@include('org.reports._filter', ['routeName' => 'admin.org.reports.index', 'courses' => $courses, 'orgs' => $orgs, 'orgId' => $orgId])

{{-- Stat cards --}}
<div class="row row-cards mb-3">
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total peserta aktif</div>
                <div class="h1 mt-1">{{ number_format($summary['learnerCount']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Kursus diikuti</div>
                <div class="h1 mt-1">{{ number_format($summary['enrollments']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Kursus diselesaikan</div>
                <div class="h1 mt-1">
                    {{ number_format($summary['completed']) }}
                    @if ($summary['enrollments'] > 0)
                        <span class="text-muted fs-4">
                            ({{ round($summary['completed'] / $summary['enrollments'] * 100) }}%)
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Sertifikat diterbitkan</div>
                <div class="h1 mt-1">{{ number_format($summary['certificates']) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Second row --}}
<div class="row row-cards mb-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                <div class="subheader mb-2">Rata-rata progres semua peserta</div>
                <div class="display-4 fw-bold text-primary">{{ $summary['avgProgress'] }}%</div>
                <div class="progress w-100 mt-3" style="height:8px;">
                    <div class="progress-bar" style="width:{{ $summary['avgProgress'] }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Akses cepat laporan</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('admin.org.reports.by-topic') }}" class="btn btn-outline-primary w-100">
                            Per Topik
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.org.reports.by-course') }}" class="btn btn-outline-primary w-100">
                            Per Kursus
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.org.reports.by-learner') }}" class="btn btn-outline-primary w-100">
                            Per Peserta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
