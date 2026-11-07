<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $certificate->code }}</title>
    <style>
        @page { margin: 40px; size: A4 landscape; }
        body { font-family: DejaVu Sans, sans-serif; color: #0A2A28; text-align: center; }
        .frame { border: 4px solid #0E6B5C; padding: 48px 32px; }
        .eyebrow { text-transform: uppercase; letter-spacing: 4px; font-size: 11px; color: #546663; margin-bottom: 8px; }
        .issuer { font-size: 22px; font-weight: bold; color: #0E6B5C; margin-bottom: 24px; }
        .label { color: #546663; font-size: 13px; margin-top: 18px; }
        .name { font-size: 30px; font-weight: bold; margin: 6px 0 18px; }
        .course { font-size: 20px; font-weight: bold; margin: 6px 0 24px; }
        .meta { margin-top: 28px; font-size: 12px; color: #546663; }
        .code { font-weight: bold; color: #0A2A28; }
    </style>
</head>
<body>
    @if (! empty($backgroundDataUri))
        <img src="{{ $backgroundDataUri }}" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0.18;z-index:0;" alt="">
    @endif
    <div class="frame" style="position:relative;z-index:1;">
        <div class="eyebrow">Sertifikat penyelesaian</div>
        <div class="issuer">{{ config('app.name') }}</div>

        <div class="label">Diberikan kepada</div>
        <div class="name">{{ $certificate->user->name }}</div>

        <div class="label">atas keberhasilan menyelesaikan kursus</div>
        <div class="course">{{ $certificate->course->getTranslation('title', app()->getLocale()) }}</div>

        <div class="meta">
            Tanggal terbit: {{ $certificate->issued_at->translatedFormat('d F Y') }}<br>
            Kode sertifikat: <span class="code">{{ $certificate->code }}</span>
        </div>

        <div class="meta" style="margin-top: 32px;">
            Keaslian sertifikat dapat diverifikasi melalui kode di atas.
        </div>
    </div>
</body>
</html>

