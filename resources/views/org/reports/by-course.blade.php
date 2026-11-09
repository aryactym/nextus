@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi</div>
            <h2 class="page-title">Laporan Capaian</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.org.reports.export', 'course') }}?{{ http_build_query(array_filter($f)) }}"
               class="btn btn-outline-secondary btn-sm">
                Unduh CSV
            </a>
        </div>
    </div>
</div>

@include('org.reports._tabs')
@include('org.reports._filter', ['routeName' => 'admin.org.reports.by-course', 'orgs' => $orgs, 'orgId' => $orgId])

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Capaian per kursus</h3>
        <div class="card-actions">
            <span class="badge bg-secondary-lt">{{ $rows->count() }} kursus</span>
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
                        <th>Kursus</th>
                        <th>Topik</th>
                        <th class="text-end">Peserta</th>
                        <th class="text-end">Selesai</th>
                        <th>Rata-rata progres</th>
                        <th class="text-end">Sertifikat</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td class="fw-medium">{{ $row->course_title }}</td>
                            <td class="text-secondary small">{{ $row->category_name }}</td>
                            <td class="text-end">{{ number_format($row->enrolled) }}</td>
                            <td class="text-end">{{ number_format($row->completed) }}</td>
                            <td style="min-width:130px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar" style="width:{{ $row->avg_progress }}%"></div>
                                    </div>
                                    <span class="text-muted small">{{ $row->avg_progress }}%</span>
                                </div>
                            </td>
                            <td class="text-end">{{ number_format($row->certificates) }}</td>
                            <td>
                                <a href="{{ route('admin.org.reports.by-course.detail', $row->course_uuid) }}?{{ http_build_query(array_filter($f)) }}"
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
