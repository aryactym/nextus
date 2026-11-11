<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

    public function thumbnail(Request $request, string $path): Response
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true) || str_contains($path, '..')) {
            abort(404, 'File tidak ditemukan.');
        }

        $disk = Storage::disk('uploads');

        if (! $disk->exists($path)) {
            // Fallback agar gambar tidak 404 pada instalasi awal
            $fallback = base_path('database/seeders/data/uploads/'.$path);
            if (is_file($fallback)) {
                return response()->file($fallback, ['Cache-Control' => 'public, max-age=86400']);
            }

            $publicFallback = public_path('images/course-1.webp');
            if (is_file($publicFallback)) {
                return response()->file($publicFallback, ['Cache-Control' => 'public, max-age=86400']);
            }

            abort(404, 'File tidak ditemukan.');
        }

        return $disk->response($path, null, ['Cache-Control' => 'public, max-age=86400']);
    }
}
