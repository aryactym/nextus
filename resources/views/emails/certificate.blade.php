<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Sertifikat terbit</title></head>
<body style="font-family:Arial,sans-serif;color:#0A2A28;padding:24px;">
  <div style="max-width:560px;margin:0 auto;">
    <h1 style="color:#0E6B5C;">Sertifikat penyelesaian terbit</h1>
    <p>Halo {{ $certificate->user->name }},</p>
    <p>Selamat! Anda telah menyelesaikan kursus <strong>{{ $certificate->course->getTranslation('title', 'id') }}</strong>.</p>
    <p>Kode sertifikat: <strong>{{ $certificate->code }}</strong></p>
    <p><a href="{{ route('certificates.show', $certificate->code) }}" style="color:#0E6B5C;">Lihat sertifikat</a></p>
  </div>
</body>
</html>

