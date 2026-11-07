@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Pembayaran</div>
            <h2 class="page-title">Detail Pesanan</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Kembali ke daftar</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Informasi pesanan</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Order ID</dt>
                    <dd class="col-sm-8"><code>{{ $order->midtrans_order_id }}</code></dd>

                    <dt class="col-sm-4">Pengguna</dt>
                    <dd class="col-sm-8">
                        {{ $order->user?->name ?? '-' }} ({{ $order->user?->email ?? '-' }})
                    </dd>

                    <dt class="col-sm-4">Kursus</dt>
                    <dd class="col-sm-8">{{ $order->course?->title ?? '-' }}</dd>

                    <dt class="col-sm-4">Tipe</dt>
                    <dd class="col-sm-8">{{ ucfirst($order->order_type) }}</dd>

                    <dt class="col-sm-4">Jumlah</dt>
                    <dd class="col-sm-8">
                        {{ number_format($order->amount, 0, ',', '.') }} {{ $order->currency }}
                    </dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @php
                            $statusBadges = [
                                'pending' => 'bg-orange-lt',
                                'paid' => 'bg-green-lt',
                                'failed' => 'bg-red-lt',
                                'expired' => 'bg-gray-lt',
                            ];
                        @endphp
                        <span class="badge {{ $statusBadges[$order->status] ?? 'bg-secondary-lt' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Metode pembayaran</dt>
                    <dd class="col-sm-8">{{ ucfirst($order->payment_type ?? '-') }}</dd>

                    <dt class="col-sm-4">Dibuat</dt>
                    <dd class="col-sm-8">{{ $order->created_at?->format('d M Y H:i') ?? '-' }}</dd>

                    @if ($order->paid_at)
                        <dt class="col-sm-4">Dibayar pada</dt>
                        <dd class="col-sm-8">{{ $order->paid_at->format('d M Y H:i') }}</dd>
                    @endif

                    @if ($order->expired_at)
                        <dt class="col-sm-4">Kadaluarsa pada</dt>
                        <dd class="col-sm-8">{{ $order->expired_at->format('d M Y H:i') }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Riwayat notifikasi webhook</h3>
            </div>
            <div class="card-body">
                @if ($order->events->isEmpty())
                    <p class="text-secondary text-center py-4">Belum ada notifikasi webhook.</p>
                @else
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th class="w-1">#</th>
                                    <th>Waktu terima</th>
                                    <th>Tanda tangan valid</th>
                                    <th class="w-1 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($order->events as $event)
                                <tr>
                                    <td class="text-secondary">{{ $loop->index }}</td>
                                    <td>{{ $event->received_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td>
                                        @if ($event->signature_valid)
                                            <span class="badge bg-green-lt">Valid</span>
                                        @else
                                            <span class="badge bg-red-lt">Tidak valid</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-secondary"
                                                onclick="togglePayload({{ $event->id }})">
                                            Lihat payload
                                        </button>
                                    </td>
                                </tr>
                                <tr id="payload-{{ $event->id }}" style="display: none;">
                                    <td colspan="4">
                                        <pre class="bg-gray-lt p-3 rounded overflow-auto small">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi cepat</h3>
            </div>
            <div class="card-body d-grid gap-2">
                @if ($order->status !== 'paid')
                    <a href="{{ route('payments.show', $order) }}" class="btn btn-outline-primary">
                        Buka halaman pembayaran
                    </a>
                @endif
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Kembali ke daftar</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePayload(eventId) {
        const el = document.getElementById('payload-' + eventId);
        if (el) {
            el.style.display = el.style.display === 'none' ? '' : 'none';
        }
    }
</script>
@endpush
@endsection
