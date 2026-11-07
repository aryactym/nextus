<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Email uji {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:24px;background:#F5F7F4;font-family:Arial,Helvetica,sans-serif;color:#0A2A28;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #E3E9E6;border-radius:12px;padding:24px;">
        <h1 style="margin:0 0 12px;font-size:20px;color:#0E6B5C;">Email uji berhasil</h1>
        <p style="margin:0 0 12px;line-height:1.6;">
            Konfigurasi SMTP {{ config('app.name') }} berhasil mengirim email ini.
            Jika Anda melihat pesan ini di Mailpit, pengaturan email sudah berjalan.
        </p>
        <p style="margin:0;line-height:1.6;color:#546663;font-size:14px;">
            Pengirim: {{ config('mail.from.address') }}
        </p>
    </div>
</body>
</html>
