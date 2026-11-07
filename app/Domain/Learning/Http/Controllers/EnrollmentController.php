<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Mail\EnrollmentConfirmation;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Payment\Models\Order;
use App\Http\Controllers\Controller;
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
            ->firstOrFail();

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
}
