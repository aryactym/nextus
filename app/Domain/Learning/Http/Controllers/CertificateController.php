<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Learning\Models\Certificate;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    public function show(Request $request, string $code)
    {
        $certificate = Certificate::query()
            ->where('code', $code)
            ->with(['course', 'user'])
            ->firstOrFail();

        abort_unless($request->user()?->id === $certificate->user_id, 403);
        abort_if($certificate->isRevoked(), 404);

        $qr = QrCode::size(200)->format('svg')->generate(route('certificates.verify', $certificate->code));

        return view('sertifikat', [
            'nama' => $certificate->user->name,
            'kursus' => $certificate->course->getTranslation('title', app()->getLocale()),
            'tanggal' => $certificate->issued_at->translatedFormat('d F Y'),
            'kode' => $certificate->code,
            'penandatangan' => null,
            'jabatan' => null,
            'tandaTangan' => null,
            'qr' => 'data:image/svg+xml;base64,'.base64_encode((string) $qr),
        ]);
    }

    public function download(Request $request, string $code): Response
    {
        $certificate = Certificate::query()
            ->where('code', $code)
            ->with(['course', 'user'])
            ->firstOrFail();

        abort_unless($request->user()?->id === $certificate->user_id, 403);
        abort_if($certificate->isRevoked(), 404);

        $qr = QrCode::size(200)->format('svg')->generate(route('certificates.verify', $certificate->code));

        $pdf = Pdf::loadView('sertifikat', [
            'nama' => $certificate->user->name,
            'kursus' => $certificate->course->getTranslation('title', app()->getLocale()),
            'tanggal' => $certificate->issued_at->translatedFormat('d F Y'),
            'kode' => $certificate->code,
            'penandatangan' => null,
            'jabatan' => null,
            'tandaTangan' => null,
            'qr' => 'data:image/svg+xml;base64,'.base64_encode((string) $qr),
        ])->setPaper('A4', 'landscape');

        return $pdf->download('sertifikat-'.$certificate->code.'.pdf');
    }

    public function verify(Request $request, ?string $code = null)
    {
        if (! $code && $request->filled('kode')) {
            return redirect()->route('certificates.verify', ['code' => trim((string) $request->input('kode'))]);
        }

        $certificate = null;
        $status = null;

        if ($code) {
            $certificate = Certificate::query()
                ->where('code', $code)
                ->with(['course', 'user'])
                ->first();
            $status = $certificate
                ? ($certificate->isRevoked() ? 'revoked' : 'valid')
                : 'not_found';
        }

        return view('pages.verifikasi', compact('code', 'certificate', 'status'));
    }
}
