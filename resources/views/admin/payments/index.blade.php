@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Pembayaran</div>
            <h2 class="page-title">Pesanan</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 w-100" id="payments-filter-form">
            {{-- Hidden fields Litepicker mengisi --}}
            <input type="hidden" name="date_from" id="date_from" value="{{ $dateFrom }}">
            <input type="hidden" name="date_to" id="date_to" value="{{ $dateTo }}">

            <div class="col-12 col-md-4">
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
                           placeholder="Cari order ID, nama, atau email">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <select name="course" id="course-select" class="form-select" aria-label="Filter pelatihan">
                    <option value="">Semua pelatihan</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) $courseId === (string) $course->id)>
                            {{ $course->getTranslation('title', 'id') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="status" class="form-select" aria-label="Filter status">
                    <option value="">Semua status</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="paid" @selected($status === 'paid')>Lunas</option>
                    <option value="failed" @selected($status === 'failed')>Gagal</option>
                    <option value="expired" @selected($status === 'expired')>Kadaluarsa</option>
                </select>
            </div>

            {{-- Date range picker — satu input, Litepicker menulis ke dua hidden fields --}}
            <div class="col-12 col-md-2">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="icon" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </span>
                    <input type="text" id="daterange-picker" class="form-control"
                           placeholder="Pilih periode"
                           value="{{ ($dateFrom && $dateTo) ? $dateFrom.' — '.$dateTo : ($dateFrom ?: '') }}"
                           autocomplete="off" readonly>
                </div>
            </div>

            <div class="col-12 col-md-auto d-flex gap-2 align-items-start">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($q !== '' || $status !== '' || $courseId !== '' || $dateFrom !== '' || $dateTo !== '')
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-link">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table card-table table-vcenter">
            <thead>
                <tr>
                    <th class="w-1">#</th>
                    <th>Order ID</th>
                    <th>Pengguna</th>
                    <th>Pelatihan</th>
                    <th class="text-end">Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
                @php
                    $statusLabels = ['pending' => 'Pending', 'paid' => 'Lunas', 'failed' => 'Gagal', 'expired' => 'Kadaluarsa'];
                    $statusBadges = ['pending' => 'bg-orange-lt', 'paid' => 'bg-green-lt', 'failed' => 'bg-red-lt', 'expired' => 'bg-gray-lt'];
                @endphp
                <tr>
                    <td class="text-secondary">{{ $orders->firstItem() + $loop->index }}</td>
                    <td><code class="text-secondary">{{ $order->midtrans_order_id }}</code></td>
                    <td>
                        <div>{{ $order->user?->name ?? '-' }}</div>
                        <div class="small text-secondary">{{ $order->user?->email ?? '-' }}</div>
                    </td>
                    <td>{{ $order->course?->getTranslation('title', 'id') ?? '-' }}</td>
                    <td class="text-end">{{ number_format($order->amount, 0, ',', '.') }} {{ $order->currency }}</td>
                    <td>
                        <span class="badge {{ $statusBadges[$order->status] ?? 'bg-secondary-lt' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td class="text-secondary small">
                        {{ $order->created_at?->format('d M Y') ?? '-' }}
                        @if ($order->paid_at)
                            <div class="text-green">Lunas {{ $order->paid_at->format('d M Y') }}</div>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.payments.show', $order->uuid) }}"
                           class="btn btn-sm btn-outline-primary">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-secondary py-4">
                        Tidak ada pesanan yang cocok dengan filter.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $orders->withQueryString()->links('vendor.pagination.tabler') }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/tom-select/dist/css/tom-select.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/litepicker/dist/litepicker.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/tom-select/dist/js/tom-select.base.min.js"></script>
<script>
    (function () {
        const picker = new Litepicker({
            element: document.getElementById('daterange-picker'),
            singleMode: false,
            allowRepick: true,
            numberOfMonths: 2,
            numberOfColumns: 2,
            format: 'YYYY-MM-DD',
            lang: 'id-ID',
            setup(picker) {
                picker.on('selected', (dateStart, dateEnd) => {
                    document.getElementById('date_from').value = dateStart.format('YYYY-MM-DD');
                    document.getElementById('date_to').value   = dateEnd.format('YYYY-MM-DD');
                });
                picker.on('clear:selection', () => {
                    document.getElementById('date_from').value = '';
                    document.getElementById('date_to').value   = '';
                });
            },
        });

        // Pulihkan tampilan display dari nilai hidden saat halaman dimuat ulang
        @if ($dateFrom && $dateTo)
            picker.setDateRange('{{ $dateFrom }}', '{{ $dateTo }}');
        @elseif ($dateFrom)
            picker.setDate('{{ $dateFrom }}');
        @endif
    }());

    new TomSelect('#course-select', {
        placeholder: 'Semua pelatihan',
        allowEmptyOption: true,
        maxOptions: null,
    });
</script>
@endpush
