{{--
    Template sertifikat NextUs, A4 landscape (297 x 210 mm).

    Variabel yang dipakai (ganti sesuai controller Anda):
      $nama           string  nama peserta
      $kursus         string  judul kursus
      $tanggal        string  mis. "07 November 2026"
      $kode           string  mis. "NX-2026-G5TJQ3CP"
      $penandatangan  string  (opsional) nama penandatangan
      $jabatan        string  (opsional) jabatan penandatangan
      $tandaTangan    string  (opsional) path file PNG tanda tangan, mis. public_path('images/ttd.png')
      $qr             string  (opsional) data URI PNG QR verifikasi

    Dibuat agar aman untuk dompdf (tanpa flex/grid/gradient/opacity) dan juga tampil sama di Chromium.
    Layout memakai posisi absolut dengan satuan mm supaya tidak ada elemen yang "bocor" ke halaman kedua.
--}}
@php
    $nama          = $nama ?? 'MUHAMAD ARYA KURNIAWAN';
    $kursus        = $kursus ?? 'Pengantar Zakat untuk Relawan';
    $tanggal       = $tanggal ?? '07 November 2026';
    $kode          = $kode ?? 'NX-2026-G5TJQ3CP';
    $penandatangan = $penandatangan ?? 'Tim NextUs';
    $jabatan       = $jabatan ?? 'Penyelenggara Kursus';

    // Perkecil ukuran nama otomatis bila panjang agar tetap satu baris.
    $panjangNama = mb_strlen($nama);
    $ukuranNama  = $panjangNama > 40 ? 19 : ($panjangNama > 32 ? 23 : ($panjangNama > 26 ? 27 : 31));

    $panjangKursus = mb_strlen($kursus);
    $ukuranKursus  = $panjangKursus > 70 ? 14 : ($panjangKursus > 48 ? 17 : 20);

    $logoPath = public_path('images/logo-nextus.png');
    $logoUrl = asset('images/logo-nextus.png');
    $hasLogo = is_file($logoPath);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $kode }} | NextUs</title>
    <style>
        @page { size: A4 landscape; margin: 0; }

        html, body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 209mm;
            background: #F5F7F4;
            font-family: 'Plus Jakarta Sans', 'DejaVu Sans', Arial, sans-serif;
            color: #0A2A28;
        }

        .page {
            position: relative;
            width: 297mm;
            height: 209mm;
            overflow: hidden;
            page-break-after: avoid;
        }

        /* Bingkai luar zamrud */
        .frame {
            position: absolute;
            left: 9mm;
            top: 9mm;
            width: 275mm;
            height: 188mm;
            border: 2mm solid #0E6B5C;
            background: #FFFFFF;
        }

        /* Garis tipis fajar di dalam bingkai */
        .frame-inner {
            position: absolute;
            left: 14mm;
            top: 14mm;
            width: 268.2mm;
            height: 181.2mm;
            border: 0.4mm solid #F5A524;
        }

        /* Sudut siku fajar */
        .corner { position: absolute; width: 9mm; height: 9mm; }
        .c-tl { left: 17mm;  top: 17mm;  border-top: 1.2mm solid #F5A524; border-left: 1.2mm solid #F5A524; }
        .c-tr { right: 17mm; top: 17mm;  border-top: 1.2mm solid #F5A524; border-right: 1.2mm solid #F5A524; }
        .c-bl { left: 17mm;  bottom: 17mm; border-bottom: 1.2mm solid #F5A524; border-left: 1.2mm solid #F5A524; }
        .c-br { right: 17mm; bottom: 17mm; border-bottom: 1.2mm solid #F5A524; border-right: 1.2mm solid #F5A524; }

        /* Blok teks utama */
        .main {
            position: absolute;
            left: 24mm;
            top: 21mm;
            width: 249mm;
            text-align: center;
        }

        .logo { height: 13mm; margin: 0 auto; }
        .brand-text { font-size: 24pt; font-weight: bold; color: #0E6B5C; margin: 0; }

        .kicker {
            margin: 5mm 0 0 0;
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 1.2mm;
            color: #0E6B5C;
        }

        .rule {
            width: 28mm;
            height: 0.9mm;
            background: #F5A524;
            margin: 4mm auto 0 auto;
        }

        .lead { margin: 8mm 0 0 0; font-size: 11pt; color: #546663; }

        .name {
            margin: 3mm auto 0 auto;
            padding-bottom: 3mm;
            width: 215mm;
            border-bottom: 0.4mm solid #E3E9E6;
            font-weight: bold;
            line-height: 1.2;
            color: #0A2A28;
        }

        .course-lead { margin: 7mm 0 0 0; font-size: 11pt; color: #546663; }

        .course {
            margin: 2.5mm auto 0 auto;
            width: 225mm;
            font-weight: bold;
            line-height: 1.3;
            color: #0E6B5C;
        }

        /* Baris bawah: tanggal/kode, segel, tanda tangan */
        .foot {
            position: absolute;
            left: 30mm;
            top: 132mm;
            width: 237mm;
        }
        .foot table { width: 100%; border-collapse: collapse; }
        .foot td { vertical-align: bottom; padding: 0; }
        .col-l { width: 38%; text-align: left; }
        .col-c { width: 24%; text-align: center; }
        .col-r { width: 38%; text-align: center; }

        .label { font-size: 8.5pt; color: #546663; margin: 0; }
        .value { font-size: 11pt; font-weight: bold; color: #0A2A28; margin: 0.8mm 0 0 0; }
        .code  { font-size: 11pt; font-weight: bold; color: #0E6B5C; letter-spacing: 0.3mm; margin: 0.8mm 0 0 0; }

        .qr { width: 20mm; height: 20mm; margin-top: 3mm; }

        /* Segel: dua lingkaran bersarang */
        .seal-outer {
            width: 27mm;
            height: 27mm;
            margin: 0 auto;
            border: 1mm solid #F5A524;
            border-radius: 50%;
        }
        .seal-inner {
            width: 22mm;
            height: 22mm;
            margin: 1.5mm auto 0 auto;
            border: 0.4mm solid #0E6B5C;
            border-radius: 50%;
            background: #0E6B5C;
            text-align: center;
        }
        .seal-name  { padding-top: 6.2mm; font-size: 10.5pt; font-weight: bold; color: #FFFFFF; }
        .seal-sub   { margin-top: 0.8mm; font-size: 5.5pt; letter-spacing: 0.3mm; color: #F5A524; font-weight: bold; }

        .sign-img  { height: 15mm; margin: 0 auto; }
        .sign-gap  { height: 15mm; }
        .sign-line { width: 62mm; margin: 1mm auto 0 auto; border-top: 0.4mm solid #0A2A28; }
        .sign-name { font-size: 11pt; font-weight: bold; margin: 1.5mm 0 0 0; color: #0A2A28; }
        .sign-role { font-size: 8.5pt; color: #546663; margin: 0.5mm 0 0 0; }

        /* Keterangan verifikasi */
        .verify {
            position: absolute;
            left: 24mm;
            top: 176mm;
            width: 249mm;
            text-align: center;
            font-size: 8.5pt;
            color: #546663;
        }
        .verify strong { color: #0A2A28; }
    </style>
</head>
<body>
<div class="page">

    <div class="frame"></div>
    <div class="frame-inner"></div>
    <div class="corner c-tl"></div>
    <div class="corner c-tr"></div>
    <div class="corner c-bl"></div>
    <div class="corner c-br"></div>

    <div class="main">
        @if ($hasLogo)
            <img class="logo" src="{{ $logoUrl }}" alt="NextUs">
        @else
            <p class="brand-text">NextUs</p>
        @endif

        <p class="kicker">Sertifikat penyelesaian</p>
        <div class="rule"></div>

        <p class="lead">Diberikan kepada</p>
        <div class="name" style="font-size: {{ $ukuranNama }}pt;">{{ $nama }}</div>

        <p class="course-lead">atas keberhasilan menyelesaikan kursus</p>
        <div class="course" style="font-size: {{ $ukuranKursus }}pt;">{{ $kursus }}</div>
    </div>

    <div class="foot">
        <table>
            <tr>
                <td class="col-l">
                    <p class="label">Tanggal terbit</p>
                    <p class="value">{{ $tanggal }}</p>
                    <p class="label" style="margin-top: 4mm;">Kode sertifikat</p>
                    <p class="code">{{ $kode }}</p>
                    @isset($qr)
                        <img class="qr" src="{{ $qr }}" alt="QR verifikasi">
                    @endisset
                </td>

                <td class="col-c">
                    <div class="seal-outer">
                        <div class="seal-inner">
                            <div class="seal-name">NextUs</div>
                            <div class="seal-sub">TERVERIFIKASI</div>
                        </div>
                    </div>
                </td>

                <td class="col-r">
                    @if (!empty($tandaTangan) && is_file($tandaTangan))
                        <img class="sign-img" src="{{ $tandaTangan }}" alt="Tanda tangan">
                    @else
                        <div class="sign-gap"></div>
                    @endif
                    <div class="sign-line"></div>
                    <p class="sign-name">{{ $penandatangan }}</p>
                    <p class="sign-role">{{ $jabatan }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="verify">
        Keaslian sertifikat dapat diverifikasi di <strong>{{ url('/verifikasi') }}</strong>
        dengan memasukkan kode <strong>{{ $kode }}</strong>.
    </div>

</div>
</body>
</html>
