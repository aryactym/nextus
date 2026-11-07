<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Pengingat kursus</title></head>
<body style="font-family:Arial,sans-serif;color:#0A2A28;padding:24px;">
  <div style="max-width:560px;margin:0 auto;">
    <h1 style="color:#0E6B5C;">Jangan berhenti di tengah jalan</h1>
    <p>Halo {{ $enrollment->user->name }},</p>
    <p>Kursus <strong>{{ $enrollment->course->getTranslation('title', 'id') }}</strong> Anda masih {{ $enrollment->progress_percent }}%.</p>
    <p>Lanjutkan belajar hari ini agar bisa menyelesaikan dan mendapatkan sertifikat.</p>
    <p><a href="{{ route('catalog.show', $enrollment->course->slug) }}" style="color:#0E6B5C;">Lanjutkan kursus</a></p>
  </div>
</body>
</html>
