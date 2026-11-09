<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportLearnersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 120;

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    public function __construct(
        private readonly int $organizationId,
        private readonly array $rows,
        private readonly string $cacheKey,
    ) {}

    public function handle(): void
    {
        $results = [
            'total' => count($this->rows),
            'success' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        foreach ($this->rows as $index => $row) {
            $rowNumber = $index + 2; // baris 1 = header

            $validator = Validator::make($row, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
                'nama_jabatan' => ['nullable', 'string', 'max:255'],
                'kategori_jabatan' => ['nullable', 'string', 'max:100'],
            ]);

            if ($validator->fails()) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $row['email'] ?? '(kosong)',
                    'reason' => implode(', ', $validator->errors()->all()),
                ];
                $results['skipped']++;

                continue;
            }

            // Skip jika email sudah terdaftar
            if (User::withoutGlobalScopes()->where('email', $row['email'])->exists()) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $row['email'],
                    'reason' => 'Email sudah terdaftar, dilewati.',
                ];
                $results['skipped']++;

                continue;
            }

            try {
                $user = User::withoutGlobalScopes()->create([
                    'name' => trim($row['name']),
                    'email' => strtolower(trim($row['email'])),
                    'password' => Hash::make($row['password']),
                    'organization_id' => $this->organizationId,
                    'nama_jabatan' => filled($row['nama_jabatan'] ?? null) ? trim($row['nama_jabatan']) : null,
                    'kategori_jabatan' => filled($row['kategori_jabatan'] ?? null) ? trim($row['kategori_jabatan']) : null,
                    'locale' => 'id',
                ]);

                $user->assignRole('Learner');
                $results['success']++;
            } catch (\Throwable $e) {
                Log::error('ImportLearnersJob row error', [
                    'row' => $rowNumber,
                    'email' => $row['email'],
                    'error' => $e->getMessage(),
                ]);

                $results['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $row['email'],
                    'reason' => 'Gagal disimpan: '.$e->getMessage(),
                ];
                $results['skipped']++;
            }
        }

        // Simpan hasil ke cache selama 30 menit agar controller bisa membacanya
        Cache::put($this->cacheKey, $results, now()->addMinutes(30));
    }

    public function failed(\Throwable $exception): void
    {
        Cache::put($this->cacheKey, [
            'total' => count($this->rows),
            'success' => 0,
            'skipped' => count($this->rows),
            'errors' => [['row' => 0, 'email' => '—', 'reason' => 'Job gagal: '.$exception->getMessage()]],
        ], now()->addMinutes(30));
    }
}
