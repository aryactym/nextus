<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Pendaftaran kursus</title></head>
<body style="font-family:Arial,sans-serif;color:#0A2A28;padding:24px;">
  <div style="max-width:560px;margin:0 auto;">
    <h1 style="color:#0E6B5C;">Pendaftaran berhasil</h1>
    <p>Halo {{ $enrollment->user->name }},</p>
    <p>Anda berhasil mendaftar kursus <strong>{{ $enrollment->course->getTranslation('title', 'id') }}</strong>.</p>
    <p><a href="{{ route('catalog.show', $enrollment->course->slug) }}" style="color:#0E6B5C;">Buka kursus</a></p>
  </div>
</body>
</html>

