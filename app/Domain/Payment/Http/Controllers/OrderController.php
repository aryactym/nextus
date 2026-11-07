<?php

declare(strict_types=1);

namespace App\Domain\Payment\Http\Controllers;

use App\Domain\Payment\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage payments'), 403);

        $q = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', '');

        $orders = Order::query()
            ->with(['user', 'course'])
            ->when($q !== '', fn ($query) => $query->where(function ($query) use ($q): void {
                $query->where('midtrans_order_id', 'like', '%'.$q.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%'))
                    ->orWhereHas('course', fn ($c) => $c->where('title', 'like', '%'.$q.'%'));
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('orders', 'q', 'status'));
    }

    public function show(Order $order): View
    {
        abort_unless(auth()->user()->can('manage payments'), 403);

        $order->load(['user', 'course', 'events']);

        return view('admin.payments.show', compact('order'));
    }
}
