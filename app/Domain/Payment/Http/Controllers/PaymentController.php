<?php

declare(strict_types=1);

namespace App\Domain\Payment\Http\Controllers;

use App\Domain\Payment\Models\Order;
use App\Domain\Settings\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, Order $order): View
    {
        abort_unless($request->user()?->id === $order->user_id, 403);

        if ($order->status === 'paid') {
            return redirect()->route('catalog.show', $order->course?->slug ?? '');
        }

        return view('payments.show', [
            'order' => $order,
            'clientKey' => Setting::get('midtrans_client_key'),
            'isProduction' => Setting::get('midtrans_is_production') === '1',
        ]);
    }
}
