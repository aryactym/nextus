<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Selamat datang</title></head>
<body style="font-family:Arial,sans-serif;color:#0A2A28;padding:24px;">
  <div style="max-width:560px;margin:0 auto;">
    <h1 style="color:#0E6B5C;">Selamat datang, {{ $user->name }}!</h1>
    <p>Akun Anda di {{ config('app.name') }} sudah aktif. Mulai jelajahi katalog kursus dan belajar mandiri.</p>
    <p><a href="{{ url('/') }}" style="color:#0E6B5C;">Buka platform</a></p>
  </div>
</body>
</html>
