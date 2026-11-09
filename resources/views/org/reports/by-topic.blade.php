@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi</div>
            <h2 class="page-title">Laporan Capaian</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.org.reports.export', 'topic') }}?{{ http_build_query(array_filter($f)) }}"
               class="btn btn-outline-secondary btn-sm">
                Unduh CSV
            </a>
        </div>
    </div>
</div>

@include('org.reports._tabs')
@include('org.reports._filter', ['routeName' => 'admin.org.reports.by-topic'])

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Capaian per topik</h3>
        <div class="card-actions">
            <span class="badge bg-secondary-lt">{{ $rows->count() }} topik</span>
        </div>
    </div>
    @if ($rows->isEmpty())
        <div class="card-body text-center text-secondary py-5">
            Belum ada data untuk periode ini.
        </div>
    @else
        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th>Topik</th>
                        <th class="text-end">Peserta terdaftar</th>
                        <th class="text-end">Selesai</th>
                        <th class="text-end">Tingkat penyelesaian</th>
                        <th>Rata-rata progres</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        @php
                            $completionRate = $row->enrolled > 0
                                ? round($row->completed / $row->enrolled * 100)
                                : 0;
                        @endphp
                        <tr>
                            <td class="fw-medium">{{ $row->category_name }}</td>
                            <td class="text-end">{{ number_format($row->enrolled) }}</td>
                            <td class="text-end">{{ number_format($row->completed) }}</td>
                            <td class="text-end">
                                <span class="badge {{ $completionRate >= 70 ? 'bg-green-lt' : ($completionRate >= 40 ? 'bg-orange-lt' : 'bg-red-lt') }}">
                                    {{ $completionRate }}%
                                </span>
                            </td>
                            <td style="min-width:140px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar" style="width:{{ $row->avg_progress }}%"></div>
                                    </div>
                                    <span class="text-muted small">{{ $row->avg_progress }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
