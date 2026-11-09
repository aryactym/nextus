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
        $certificate = $this->findAccessibleCertificate($request, $code);

        $qr = QrCode::size(200)->format('svg')->generate(route('certificates.verify', $certificate->code));

        return view('sertifikat', [
            'nama' => $certificate->user->name,
            'kursus' => $certificate->course?->getTranslation('title', app()->getLocale()) ?? 'Kursus',
            'tanggal' => $certificate->issued_at->translatedFormat('d F Y'),
            'kode' => $certificate->code,
            'penandatangan' => null,
            'jabatan' => null,
            'tandaTangan' => null,
            'qr' => 'data:image/svg+xml;base64,'.base64_encode((string) $qr),
            'showActions' => true,
            'linkedinUrl' => $this->linkedinUrl($certificate),
        ]);
    }

    public function download(Request $request, string $code): Response
    {
        $certificate = $this->findAccessibleCertificate($request, $code);

        $qr = QrCode::size(200)->format('svg')->generate(route('certificates.verify', $certificate->code));

        $pdf = Pdf::loadView('sertifikat', [
            'nama' => $certificate->user->name,
            'kursus' => $certificate->course?->getTranslation('title', app()->getLocale()) ?? 'Kursus',
            'tanggal' => $certificate->issued_at->translatedFormat('d F Y'),
            'kode' => $certificate->code,
            'penandatangan' => null,
            'jabatan' => null,
            'tandaTangan' => null,
            'qr' => 'data:image/svg+xml;base64,'.base64_encode((string) $qr),
        ])->setPaper('A4', 'landscape');

        return $pdf->download('sertifikat-'.$certificate->code.'.pdf');
    }

    /**
     * Ambil sertifikat by kode dan pastikan user boleh mengaksesnya:
     * pemilik, Super Admin/Admin (manage users), atau Org Admin untuk peserta
     * di organisasi yang sama.
     */
    private function findAccessibleCertificate(Request $request, string $code): Certificate
    {
        $certificate = Certificate::query()
            ->where('code', $code)
            ->with([
                'course' => fn ($query) => $query->withoutGlobalScopes()->withTrashed(),
                'user' => fn ($query) => $query->withoutGlobalScopes(),
            ])
            ->firstOrFail();

        $this->authorizeCertificateAccess($request, $certificate);

        abort_if($certificate->isRevoked(), 404);

        return $certificate;
    }

    private function authorizeCertificateAccess(Request $request, Certificate $certificate): void
    {
        $user = $request->user();

        abort_unless($user !== null, 403);

        if ($user->id === $certificate->user_id) {
            return;
        }

        if ($user->can('manage users')) {
            return;
        }

        if (
            $user->can('manage org learners')
            && (int) $certificate->user?->organization_id === (int) $user->organization_id
        ) {
            return;
        }

        abort(403);
    }

    /**
     * URL "Tambahkan ke LinkedIn" untuk sertifikat ini.
     */
    private function linkedinUrl(Certificate $certificate): string
    {
        $course = $certificate->course;
        $organizationName = $course?->organization?->name ?? config('app.name');

        $params = [
            'startTask' => 'CERTIFICATION_NAME',
            'name' => $course?->getTranslation('title', 'id') ?? 'Kursus',
            'organizationName' => $organizationName,
            'issueYear' => $certificate->issued_at->format('Y'),
            'issueMonth' => $certificate->issued_at->format('n'),
            'certUrl' => route('certificates.verify', $certificate->code),
            'certId' => $certificate->code,
            'media' => $certificate->media ?? '',
            'skills' => implode(', ', $certificate->skills ?? []),
        ];

        return 'https://www.linkedin.com/profile/add?'.http_build_query($params);
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
                ->with([
                    'course' => fn ($query) => $query->withoutGlobalScopes()->withTrashed(),
                    'user' => fn ($query) => $query->withoutGlobalScopes(),
                ])
                ->first();
            $status = $certificate
                ? ($certificate->isRevoked() ? 'revoked' : 'valid')
                : 'not_found';
        }

        return view('pages.verifikasi', compact('code', 'certificate', 'status'));
    }
}
