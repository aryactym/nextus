<?php

declare(strict_types=1);

namespace App\Domain\Learning\Services;

use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Enrollment;
use Illuminate\Support\Str;

class CertificateIssuer
{
    /**
     * Terbitkan sertifikat bila enrollment selesai dan tipe sertifikat gratis.
     */
    public function issueIfEligible(Enrollment $enrollment): ?Certificate
    {
        $enrollment->loadMissing('course');

        if ($enrollment->status !== 'completed') {
            return null;
        }

        if ($enrollment->certificate()->exists()) {
            return $enrollment->certificate;
        }

        if ($enrollment->course?->certificate_type !== 'free') {
            return null;
        }

        return Certificate::create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'code' => $this->uniqueCode(),
            'issued_at' => now(),
        ]);
    }

    private function uniqueCode(): string
    {
        do {
            $code = 'NX-'.now()->format('Y').'-'.Str::upper(Str::random(8));
        } while (Certificate::query()->where('code', $code)->exists());

        return $code;
    }
}
