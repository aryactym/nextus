<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UploadService
{
    /**
     * Ekstensi yang diizinkan per kategori.
     */
    protected array $allowedExtensions = [
        'image' => ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'],
        'document' => ['pdf'],
    ];

    /**
     * MIME type yang diizinkan per ekstensi (last line of defense).
     */
    protected array $allowedMime = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'avif' => ['image/avif'],
        'gif' => ['image/gif'],
        'pdf' => ['application/pdf'],
    ];

    /**
     * Ukuran maksimal per kategori (kilobyte).
     */
    protected array $maxSizeKb = [
        'image' => 2048,   // 2 MB
        'document' => 10240, // 10 MB
    ];

    public function __construct(
        protected string $disk = 'uploads',
        protected string $folder = ''
    ) {}

    /**
     * Upload satu file ke storage di luar webroot.
     *
     * @return array ['path' => string, 'url' => string|null, 'size' => int, 'mime' => string, 'extension' => string]
     *
     * @throws ValidationException
     */
    public function upload(UploadedFile $file, string $category = 'image', ?string $folder = null): array
    {
        $validator = Validator::make(['file' => $file], [
            'file' => ['required', 'file', 'max:'.$this->maxSizeKb[$category] ?? 2048],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $originalExtension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        // Allowlist ekstensi
        if (! in_array($originalExtension, $this->allowedExtensions[$category] ?? [], true)) {
            throw new \InvalidArgumentException("Ekstensi {$originalExtension} tidak diizinkan untuk kategori {$category}.");
        }

        // Allowlist MIME (hindari polyglot / file berbahaya)
        if (! in_array($mime, $this->allowedMime[$originalExtension] ?? [], true)) {
            throw new \InvalidArgumentException("MIME type {$mime} tidak cocok dengan ekstensi {$originalExtension}.");
        }

        $realPath = $file->getRealPath();

        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'thumbnail' => 'Upload thumbnail gagal: '.$this->uploadErrorMessage($file->getError()),
            ]);
        }

        if (! $realPath || ! is_file($realPath) || filesize($realPath) === 0) {
            throw ValidationException::withMessages([
                'thumbnail' => 'File gambar kosong atau tidak dapat dibaca.',
            ]);
        }

        // Re-encode gambar untuk membersihkan metadata / polyglot
        $content = $realPath;
        if ($category === 'image') {
            try {
                $content = $this->reencodeImage($realPath, $originalExtension);
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'thumbnail' => 'File gambar tidak valid atau rusak.',
                ]);
            }
        }

        // Nama file acak (UUID) + folder per tanggal/organisasi
        $diskFolder = $folder ?? date('Y/m');
        $fileName = (string) Str::uuid().'.'.$originalExtension;
        $path = $diskFolder.'/'.$fileName;

        Storage::disk($this->disk)->put($path, file_get_contents($content), 'private');

        // Bersihkan file temp re-encode kalau ada
        if ($category === 'image' && $content !== $file->getRealPath()) {
            @unlink($content);
        }

        return [
            'path' => $path,
            'url' => null, // disajikan via signed URL controller, tidak langsung
            'size' => $file->getSize(),
            'mime' => $mime,
            'extension' => $originalExtension,
        ];
    }

    /**
     * Hapus file dari storage.
     */
    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Buat signed URL temporary untuk file privat (default 15 menit).
     */
    public function temporaryUrl(string $path, ?\DateTimeInterface $expiration = null): string
    {
        $expiration = $expiration ?? now()->addMinutes(15);

        return Storage::disk($this->disk)->temporaryUrl($path, $expiration);
    }

    /**
     * Pesan ramah untuk kode error upload PHP.
     */
    protected function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi batas server.',
            UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diunggah.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak tersedia.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk.',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP.',
            default => 'Kode error '.$code.'.',
        };
    }

    protected function reencodeImage(string $realPath, string $extension): string
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->decodePath($realPath);

        // Normalisasi orientasi & hilangkan metadata berlebih lewat re-encode
        $outputPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.(string) Str::uuid().'.'.$extension;

        $image->save($outputPath);

        return $outputPath;
    }
}
