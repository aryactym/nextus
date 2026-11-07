<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Learning\Models\Certificate;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends Controller
{
    public function show(Request $request, string $code): View
    {
        $certificate = Certificate::query()
            ->where('code', $code)
            ->with(['course', 'user'])
            ->firstOrFail();

        abort_unless($request->user()?->id === $certificate->user_id, 403);
        abort_if($certificate->isRevoked(), 404);

        return view('learning.certificate', compact('certificate'));
    }

    public function download(Request $request, string $code): Response
    {
        $certificate = Certificate::query()
            ->where('code', $code)
            ->with(['course', 'user'])
            ->firstOrFail();

        abort_unless($request->user()?->id === $certificate->user_id, 403);
        abort_if($certificate->isRevoked(), 404);

        $backgroundDataUri = null;

        if ($certificate->course->certificate_background) {
            $path = $certificate->course->certificate_background;

            if (Storage::disk('uploads')->exists($path)) {
                $contents = Storage::disk('uploads')->get($path);
                $mime = Storage::disk('uploads')->mimeType($path) ?: 'image/jpeg';
                $backgroundDataUri = 'data:'.$mime.';base64,'.base64_encode($contents);
            }
        }

        $pdf = Pdf::loadView('learning.certificate-pdf', compact('certificate', 'backgroundDataUri'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('sertifikat-'.$certificate->code.'.pdf');
    }

    public function verify(Request $request, ?string $code = null): View|RedirectResponse
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
