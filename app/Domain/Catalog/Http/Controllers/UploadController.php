<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
    public function show(Request $request, UploadService $upload, string $path): BinaryFileResponse|Response
    {
        $disk = Storage::disk('uploads');

        if (! $disk->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Kembalikan file sebagai unduhan (hindari eksekusi langsung di browser)
        return response()->download($disk->path($path), null, [
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
