<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Models\Enrollment;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $course = Course::withoutGlobalScope('organization')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if ($course->access_type !== 'free') {
            return redirect()
                ->route('catalog.show', $course->slug)
                ->with('enroll_error', 'Kursus ini memerlukan pembayaran atau donasi. Pembayaran via Midtrans segera tersedia.');
        }

        Enrollment::firstOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $course->id],
            ['status' => 'active', 'progress_percent' => 0, 'enrolled_at' => now()],
        );

        return redirect()
            ->route('catalog.show', $course->slug)
            ->with('enroll_success', 'Berhasil mendaftar! Selamat belajar.');
    }
}
