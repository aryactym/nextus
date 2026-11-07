<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Mail\EnrollmentConfirmation;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Payment\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Midtrans\Snap;

class EnrollmentController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $course = Course::withoutGlobalScope('organization')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with('requirement.prerequisiteCourse', 'requirement.pendidikan')
            ->firstOrFail();

        $requirementError = $this->checkRequirements($request->user(), $course);
        if ($requirementError !== null) {
            return redirect()
                ->route('catalog.show', $course->slug)
                ->with('enroll_error', $requirementError);
        }

        if ($course->access_type !== 'free') {
            $user = $request->user();

            $amount = max((float) $course->min_amount, 10000.0);

            $order = Order::create([
                'midtrans_order_id' => 'AF-'.strtoupper(substr($course->access_type, 0, 3)).'-'.Str::uuid(),
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_type' => 'course_access',
                'nature' => $course->access_type,
                'amount' => $amount,
                'currency' => $course->currency ?? 'IDR',
                'status' => 'pending',
                'expired_at' => now()->addHours(24),
            ]);

            try {
                $snapToken = Snap::getSnapToken([
                    'transaction_details' => [
                        'order_id' => $order->midtrans_order_id,
                        'gross_amount' => $amount,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                    ],
                    'item_details' => [[
                        'id' => $course->slug,
                        'price' => $amount,
                        'quantity' => 1,
                        'name' => $course->getTranslation('title', 'id'),
                    ]],
                ]);
            } catch (\Throwable $e) {
                $order->delete();

                return redirect()
                    ->route('catalog.show', $course->slug)
                    ->with('enroll_error', 'Gagal membuat pembayaran: '.$e->getMessage());
            }

            $order->update(['snap_token' => $snapToken]);

            return redirect()->route('payments.show', $order);
        }

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $course->id],
            ['status' => 'active', 'progress_percent' => 0, 'enrolled_at' => now()],
        );

        if ($enrollment->wasRecentlyCreated) {
            Mail::to($request->user())->send(new EnrollmentConfirmation($enrollment));
        }

        return redirect()
            ->route('catalog.show', $course->slug)
            ->with('enroll_success', 'Berhasil mendaftar! Selamat belajar.');
    }

    /**
     * Cek apakah user memenuhi requirement kursus.
     * Kembalikan pesan error jika tidak memenuhi, null jika memenuhi atau tidak ada requirement.
     */
    private function checkRequirements(?User $user, Course $course): ?string
    {
        $requirement = $course->requirement;
        if ($requirement === null) {
            return null;
        }

        // Cek pendidikan minimal peserta
        if ($requirement->pendidikan_id !== null && $requirement->pendidikan !== null) {
            $userPendidikan = $user?->pendidikan;
            if ($userPendidikan === null || $userPendidikan->level < $requirement->pendidikan->level) {
                return 'Anda harus memiliki pendidikan minimal '.$requirement->pendidikan->name.' untuk mengikuti kursus ini.';
            }
        }

        // Cek kursus prasyarat dengan nilai minimal
        if ($requirement->prerequisite_course_id !== null && $requirement->prerequisiteCourse !== null) {
            $prereqEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $requirement->prerequisite_course_id)
                ->where('status', 'completed')
                ->first();

            if ($prereqEnrollment === null) {
                return 'Anda harus telah menyelesaikan kursus '.$requirement->prerequisiteCourse->getTranslation('title', 'id').' terlebih dahulu.';
            }

            if ($requirement->min_grade !== null) {
                $bestAttempt = $prereqEnrollment->quizAttempts()
                    ->where('passed', true)
                    ->orderByDesc('score')
                    ->first();

                if ($bestAttempt === null || (int) $bestAttempt->score < (int) $requirement->min_grade) {
                    return 'Anda harus mencapai nilai minimal '.$requirement->min_grade.' pada kursus '.$requirement->prerequisiteCourse->getTranslation('title', 'id').'.';
                }
            }
        }

        return null;
    }
}
