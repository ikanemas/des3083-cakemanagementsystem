<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with('user')->latest()->get();
        $statuses = [
            Order::STATUS_PENDING => 'Pending',
            Order::STATUS_CONFIRMED => 'Confirmed',
            Order::STATUS_COMPLETED => 'Completed',
            Order::STATUS_CANCELLED => 'Cancelled',
        ];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'remark' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in([
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_CANCELLED,
                Order::STATUS_COMPLETED,
            ])],
        ]);

        $order->update($data);

        return back()->with('status', 'Order updated.');
    }
}
