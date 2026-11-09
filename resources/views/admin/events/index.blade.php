@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Konten</div>
            <h2 class="page-title">Jadwal</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="icon me-1" aria-hidden="true"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                Tambah jadwal
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.events.index') }}" class="row g-2 w-100">
            <div class="col-12 col-md-5">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="icon" aria-hidden="true">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                            <path d="M21 21l-6 -6"/>
                        </svg>
                    </span>
                    <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Cari judul">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="type" class="form-select">
                    <option value="">Semua jenis</option>
                    <option value="webinar"   @selected($type === 'webinar')>Webinar</option>
                    <option value="pelatihan" @selected($type === 'pelatihan')>Pelatihan</option>
                    <option value="batch"     @selected($type === 'batch')>Batch kursus</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    <option value="draft"  @selected($status === 'draft')>Draf</option>
                    <option value="open"   @selected($status === 'open')>Dibuka</option>
                    <option value="soon"   @selected($status === 'soon')>Segera</option>
                    <option value="full"   @selected($status === 'full')>Penuh</option>
                    <option value="closed" @selected($status === 'closed')>Selesai</option>
                </select>
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($q !== '' || $type !== '' || $status !== '')
                    <a href="{{ route('admin.events.index') }}" class="btn btn-link">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Jenis</th>
                    <th>Format</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr>
                        <td>
                            <div class="fw-medium">{{ $event->title }}</div>
                            @if ($event->course)
                                <div class="small text-muted">{{ $event->course->getTranslation('title','id') }}</div>
                            @endif
                        </td>
                        <td><span class="badge bg-azure-lt">{{ $event->typeLabel() }}</span></td>
                        <td class="text-secondary">{{ ucfirst($event->format) }}</td>
                        <td class="small">
                            <div>{{ $event->starts_at->format('d M Y') }}</div>
                            <div class="text-muted">{{ $event->starts_at->format('H:i') }}–{{ $event->ends_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="text-secondary small">{{ $event->location ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $event->statusBadge() }}">{{ $event->statusLabel() }}</span>
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="{{ route('admin.events.edit', $event->uuid) }}"
                                   class="btn btn-sm btn-outline-primary">Ubah</a>
                                <form action="{{ route('admin.events.destroy', $event->uuid) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">Belum ada jadwal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($events->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $events->withQueryString()->links('vendor.pagination.tabler') }}
        </div>
    @endif
</div>
@endsection
