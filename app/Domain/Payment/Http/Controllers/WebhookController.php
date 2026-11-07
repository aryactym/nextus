<?php

declare(strict_types=1);

namespace App\Domain\Payment\Http\Controllers;

use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Services\CertificateIssuer;
use App\Domain\Payment\Models\Order;
use App\Domain\Payment\Models\PaymentEvent;
use App\Domain\Settings\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->all();
        $orderId = (string) ($payload['order_id'] ?? '');

        $order = Order::query()->where('midtrans_order_id', $orderId)->first();

        if (! $order) {
            return response('Order not found', 404);
        }

        $serverKey = Setting::getSecret('midtrans_server_key') ?? '';
        $signature = hash('sha512', $orderId.($payload['status_code'] ?? '').($payload['gross_amount'] ?? '').$serverKey);
        $signatureValid = hash_equals($signature, (string) ($payload['signature_key'] ?? ''));

        PaymentEvent::create([
            'order_id' => $order->id,
            'payload' => $payload,
            'signature_valid' => $signatureValid,
            'received_at' => now(),
        ]);

        if (! $signatureValid) {
            return response('Invalid signature', 403);
        }

        if ($order->status === 'paid') {
            return response('OK', 200);
        }

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $paymentType = (string) ($payload['payment_type'] ?? '');

        DB::transaction(function () use ($order, $transactionStatus, $paymentType): void {
            if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                $order->update([
                    'status' => 'paid',
                    'payment_type' => $paymentType,
                    'paid_at' => now(),
                ]);

                if ($order->course_id && $order->user_id) {
                    $enrollment = Enrollment::firstOrCreate(
                        ['user_id' => $order->user_id, 'course_id' => $order->course_id],
                        ['status' => 'active', 'progress_percent' => 0, 'enrolled_at' => now()],
                    );

                    $enrollment->update(['status' => 'active']);

                    // Terbitkan sertifikat bila kursus memang gratis sertifikat atau pembayaran sudah lunas.
                    (new CertificateIssuer)->issueIfEligible($enrollment);
                }
            } elseif (in_array($transactionStatus, ['deny', 'cancel'], true)) {
                $order->update(['status' => 'failed', 'payment_type' => $paymentType]);
            } elseif ($transactionStatus === 'expire') {
                $order->update(['status' => 'expired', 'payment_type' => $paymentType]);
            } elseif ($transactionStatus === 'pending') {
                $order->update(['status' => 'pending', 'payment_type' => $paymentType]);
            }
        });

        return response('OK', 200);
    }
}
