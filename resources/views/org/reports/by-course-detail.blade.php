@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi / Laporan / Per Kursus</div>
            <h2 class="page-title">{{ $course->getTranslation('title', 'id') }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.org.reports.by-course') }}" class="btn btn-link">
                ← Kembali ke per kursus
            </a>
        </div>
    </div>
</div>

@include('org.reports._filter', ['routeName' => 'admin.org.reports.by-course.detail',
    'courseUuid' => $course->uuid, 'orgs' => collect(), 'orgId' => $orgId])

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar peserta</h3>
        <div class="card-actions">
            <span class="badge bg-secondary-lt">{{ $rows->count() }} peserta</span>
        </div>
    </div>
    @if ($rows->isEmpty())
        <div class="card-body text-center text-secondary py-5">
            Belum ada peserta di kursus ini untuk periode ini.
        </div>
    @else
        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Email</th>
                        <th>Progres</th>
                        <th>Pre-test</th>
                        <th>Post-test</th>
                        <th>Mendaftar</th>
                        <th>Selesai</th>
                        <th>Sertifikat</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td class="fw-medium">{{ $row->learner_name }}</td>
                            <td class="text-secondary small">{{ $row->learner_email }}</td>
                            <td style="min-width:130px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar" style="width:{{ $row->progress_percent }}%"></div>
                                    </div>
                                    <span class="text-muted small">{{ $row->progress_percent }}%</span>
                                </div>
                            </td>
                            <td class="text-center small">
                                {{ $row->pretest_score !== null ? $row->pretest_score : '—' }}
                            </td>
                            <td class="text-center small">
                                {{ $row->posttest_score !== null ? $row->posttest_score : '—' }}
                            </td>
                            <td class="text-secondary small">
                                {{ $row->enrolled_at ? \Carbon\Carbon::parse($row->enrolled_at)->format('d M Y') : '—' }}
                            </td>
                            <td class="small">
                                @if ($row->completed_at)
                                    <span class="badge bg-green-lt">
                                        {{ \Carbon\Carbon::parse($row->completed_at)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Belum</span>
                                @endif
                            </td>
                            <td>
                                @if ($row->has_certificate)
                                    <span class="badge bg-green-lt">Ada</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.org.learners.show', $row->user_uuid) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    Profil
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
