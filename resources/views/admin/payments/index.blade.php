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
        <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 w-100">
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
                    <input type="search" name="q" value="{{ $q }}" class="form-control"
                           placeholder="Cari order ID, nama, atau email">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select" aria-label="Filter status">
                    <option value="">Semua status</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="paid" @selected($status === 'paid')>Lunas</option>
                    <option value="failed" @selected($status === 'failed')>Gagal</option>
                    <option value="expired" @selected($status === 'expired')>Kadaluarsa</option>
                </select>
            </div>
            <div class="col-6 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($q !== '' || $status !== '')
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
                    <th>Kursus</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
                @php
                    $statusLabels = [
                        'pending' => 'Pending',
                        'paid' => 'Lunas',
                        'failed' => 'Gagal',
                        'expired' => 'Kadaluarsa',
                    ];
                    $statusBadges = [
                        'pending' => 'bg-orange-lt',
                        'paid' => 'bg-green-lt',
                        'failed' => 'bg-red-lt',
                        'expired' => 'bg-gray-lt',
                    ];
                @endphp
                <tr>
                    <td class="text-secondary">{{ $orders->firstItem() + $loop->index }}</td>
                    <td>
                        <code class="text-secondary">{{ $order->midtrans_order_id }}</code>
                    </td>
                    <td>
                        <div>{{ $order->user?->name ?? '-' }}</div>
                        <div class="small text-secondary">{{ $order->user?->email ?? '-' }}</div>
                    </td>
                    <td>{{ $order->course?->title ?? '-' }}</td>
                    <td class="text-end">{{ number_format($order->amount, 0, ',', '.') }} {{ $order->currency }}</td>
                    <td>
                        <span class="badge {{ $statusBadges[$order->status] ?? 'bg-secondary-lt' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.payments.show', $order->uuid) }}"
                           class="btn btn-sm btn-outline-primary">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary py-4">
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
