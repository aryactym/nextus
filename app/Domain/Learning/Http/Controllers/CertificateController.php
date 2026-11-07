<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Learning\Models\Certificate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
