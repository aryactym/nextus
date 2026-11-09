<?php

declare(strict_types=1);

namespace App\Domain\Payment\Http\Controllers;

use App\Domain\Catalog\Models\Course;
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
        $courseId = (string) $request->input('course', '');
        $dateFrom = (string) $request->input('date_from', '');
        $dateTo = (string) $request->input('date_to', '');

        $courses = Course::query()
            ->select('id', 'title')
            ->orderBy('title->id')
            ->get();

        $orders = Order::query()
            ->with(['user', 'course'])
            ->when($q !== '', fn ($query) => $query->where(function ($query) use ($q): void {
                $query->where('midtrans_order_id', 'like', '%'.$q.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%'))
                    ->orWhereHas('course', fn ($c) => $c->where('title->id', 'like', '%'.$q.'%')
                        ->orWhere('title->en', 'like', '%'.$q.'%'));
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($courseId !== '', fn ($query) => $query->where('course_id', $courseId))
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('orders', 'q', 'status', 'courseId', 'dateFrom', 'dateTo', 'courses'));
    }

    public function show(Order $order): View
    {
        abort_unless(auth()->user()->can('manage payments'), 403);

        $order->load(['user', 'course', 'events']);

        return view('admin.payments.show', compact('order'));
    }
}
