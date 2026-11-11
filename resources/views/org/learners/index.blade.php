@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi</div>
            <h2 class="page-title">Peserta</h2>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <a href="{{ route('admin.org.learners.import') }}" class="btn btn-outline-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="icon me-1" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Import CSV
            </a>
            <a href="{{ route('admin.org.learners.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="icon me-1" aria-hidden="true">
                    <path d="M12 5l0 14"/><path d="M5 12l14 0"/>
                </svg>
                Tambah peserta
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.org.learners.index') }}" class="row g-2 w-100">
            <div class="col-12 col-md-6">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="icon" aria-hidden="true">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                            <path d="M21 21l-6 -6"/>
                        </svg>
                    </span>
                    <input type="search" name="q" value="{{ $q }}" class="form-control"
                           placeholder="Cari nama, email, atau jabatan">
                </div>
            </div>
            @if (isset($organizations) && $organizations !== null)
                <div class="col-12 col-md-3">
                    <select name="org_id" class="form-select" aria-label="Filter organisasi">
                        <option value="">Semua organisasi</option>
                        @php $typeLabels = App\Http\Controllers\Admin\OrganizationController::TYPES; @endphp
                        @foreach ($organizations as $org)
                            <option value="{{ $org->id }}" @selected((string) ($orgId ?? '') === (string) $org->id)>
                                {{ $org->name }} — {{ $typeLabels[$org->type] ?? $org->type }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-6 col-md-3">
                <select name="status" class="form-select" aria-label="Filter status">
                    <option value="" @selected($status === '')>Semua</option>
                    <option value="active" @selected($status === 'active')>Aktif</option>
                    <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="col-6 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($q !== '' || $status !== '' || ($orgId ?? null))
                    <a href="{{ route('admin.org.learners.index') }}" class="btn btn-link">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Organisasi</th>
                    <th>Jabatan</th>
                    <th class="text-center">Kursus aktif</th>
                    <th class="text-center">Selesai</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($learners as $learner)
                    @php
                        $activeCount    = $learner->enrollments->whereNull('completed_at')->count();
                        $completedCount = $learner->enrollments->whereNotNull('completed_at')->count();
                        $isInactive     = $learner->trashed();
                    @endphp
                    <tr class="{{ $isInactive ? 'opacity-50' : '' }}">
                        <td>
                            <a href="{{ route('admin.org.learners.show', $learner->uuid) }}"
                               class="fw-medium text-reset">
                                {{ $learner->name }}
                            </a>
                            @if ($learner->nama_organisasi)
                                <div class="small text-muted">{{ $learner->nama_organisasi }}</div>
                            @endif
                        </td>
                        <td class="text-secondary">{{ $learner->email }}</td>
                        <td class="text-secondary small">{{ $learner->organization?->name ?? '-' }}</td>
                        <td class="text-secondary small">
                            {{ $learner->nama_jabatan ?? '—' }}
                            @if ($learner->kategori_jabatan)
                                <span class="badge bg-azure-lt ms-1">{{ $learner->kategori_jabatan }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $activeCount }}</td>
                        <td class="text-center">{{ $completedCount }}</td>
                        <td>
                            @if ($isInactive)
                                <span class="badge bg-gray-lt">Nonaktif</span>
                            @else
                                <span class="badge bg-green-lt">Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="{{ route('admin.org.learners.show', $learner->uuid) }}"
                                   class="btn btn-sm btn-outline-secondary">Detail</a>
                                @if (! $isInactive)
                                    <a href="{{ route('admin.org.learners.edit', $learner->uuid) }}"
                                       class="btn btn-sm btn-outline-primary">Ubah</a>
                                    <form action="{{ route('admin.org.learners.destroy', $learner->uuid) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Nonaktifkan peserta ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Nonaktifkan</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.org.learners.restore', $learner->uuid) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success">Aktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            Belum ada peserta.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($learners->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">
                {{ $learners->firstItem() }}–{{ $learners->lastItem() }} dari {{ $learners->total() }} peserta
            </p>
            <div class="ms-auto">
                {{ $learners->withQueryString()->links('vendor.pagination.tabler') }}
            </div>
        </div>
    @endif
</div>
@endsection
