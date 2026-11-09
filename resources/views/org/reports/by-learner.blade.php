@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi</div>
            <h2 class="page-title">Laporan Capaian</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.org.reports.export', 'learner') }}?{{ http_build_query(array_filter($f)) }}"
               class="btn btn-outline-secondary btn-sm">
                Unduh CSV
            </a>
        </div>
    </div>
</div>

@include('org.reports._tabs')
@include('org.reports._filter', ['routeName' => 'admin.org.reports.by-learner', 'courses' => $courses])

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Capaian per peserta</h3>
        <div class="card-actions">
            <span class="badge bg-secondary-lt">{{ $rows->count() }} peserta</span>
        </div>
    </div>
    @if ($rows->isEmpty())
        <div class="card-body text-center text-secondary py-5">
            Belum ada data untuk filter ini.
        </div>
    @else
        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Jabatan</th>
                        <th class="text-end">Diikuti</th>
                        <th class="text-end">Selesai</th>
                        <th class="text-end">Sertifikat</th>
                        <th>Rata-rata progres</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $row->learner_name }}</div>
                                <div class="text-muted small">{{ $row->learner_email }}</div>
                            </td>
                            <td class="text-secondary small">
                                {{ $row->nama_jabatan ?? '—' }}
                                @if ($row->kategori_jabatan)
                                    <span class="badge bg-azure-lt ms-1">{{ $row->kategori_jabatan }}</span>
                                @endif
                            </td>
                            <td class="text-end">{{ $row->enrolled }}</td>
                            <td class="text-end">{{ $row->completed }}</td>
                            <td class="text-end">{{ $row->certificates }}</td>
                            <td style="min-width:130px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar" style="width:{{ $row->avg_progress }}%"></div>
                                    </div>
                                    <span class="text-muted small">{{ $row->avg_progress }}%</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.org.learners.show', $row->user_uuid) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
