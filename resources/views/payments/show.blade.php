<x-layouts.public
    title="Pembayaran kursus"
    description="Selesaikan pembayaran untuk mengakses kursus."
    active-page="catalog"
>
  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home-id') }}">Beranda</a></li>
        <li><a href="{{ route('catalog.show', $order->course->slug) }}">Kursus</a></li>
        <li aria-current="page">Pembayaran</li>
      </ol>
      <h1>Pembayaran kursus</h1>
      <p>Selesaikan pembayaran untuk membuka akses dan sertifikat.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h2 class="h3">{{ $order->course->getTranslation('title', app()->getLocale()) }}</h2>
              <p class="text-secondary">Order: {{ $order->midtrans_order_id }}</p>
              <p class="h2 mb-4">Rp {{ number_format((float) $order->amount, 0, ',', '.') }}</p>

              @if ($order->status === 'paid')
                <div class="alert alert-success">Pembayaran sudah lunas.</div>
                <a class="btn btn-primary" href="{{ route('catalog.show', $order->course->slug) }}">Buka kursus</a>
              @else
                <button id="pay-button" class="btn btn-primary w-100">Bayar sekarang</button>
                <p class="text-secondary small mt-3 mb-0">
                  Pembayaran diproses oleh Midtrans (QRIS, VA, e-wallet, kartu).
                </p>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  @if ($order->status !== 'paid')
    @push('head')
      <script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
              data-client-key="{{ $clientKey }}"></script>
    @endpush
    @push('scripts')
      <script>
        document.getElementById('pay-button')?.addEventListener('click', function () {
          window.snap.pay(@json($order->snap_token), {
            onSuccess: function () { window.location.href = @json(route('catalog.show', $order->course->slug)); },
            onPending: function () { alert('Pembayaran tertunda. Selesaikan sesuai instruksi.'); },
            onError: function () { alert('Pembayaran gagal. Silakan coba lagi.'); },
            onClose: function () { /* user closed popup */ }
          });
        });
      </script>
    @endpush
  @endif
</x-layouts.public>
