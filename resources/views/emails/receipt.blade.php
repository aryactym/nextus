<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Bukti pembayaran</title></head>
<body style="font-family:Arial,sans-serif;color:#0A2A28;padding:24px;">
  <div style="max-width:560px;margin:0 auto;">
    <h1 style="color:#0E6B5C;">Bukti pembayaran</h1>
    <p>Halo {{ $order->user->name }},</p>
    <p>Pembayaran Anda telah kami terima.</p>
    <table style="width:100%;border-collapse:collapse;margin:12px 0;">
      <tr><td style="padding:6px 0;color:#546663;">Order</td><td style="padding:6px 0;font-weight:bold;">{{ $order->midtrans_order_id }}</td></tr>
      <tr><td style="padding:6px 0;color:#546663;">Kursus</td><td style="padding:6px 0;">{{ $order->course?->getTranslation('title', 'id') }}</td></tr>
      <tr><td style="padding:6px 0;color:#546663;">Jumlah</td><td style="padding:6px 0;">Rp {{ number_format((float) $order->amount, 0, ',', '.') }}</td></tr>
      <tr><td style="padding:6px 0;color:#546663;">Metode</td><td style="padding:6px 0;">{{ $order->payment_type }}</td></tr>
      <tr><td style="padding:6px 0;color:#546663;">Tanggal</td><td style="padding:6px 0;">{{ $order->paid_at?->translatedFormat('d F Y H:i') }}</td></tr>
    </table>
    <p><a href="{{ $order->course ? route('catalog.show', $order->course->slug) : url('/') }}" style="color:#0E6B5C;">Buka kursus</a></p>
  </div>
</body>
</html>
