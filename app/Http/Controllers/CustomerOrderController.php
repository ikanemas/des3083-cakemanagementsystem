<?php

namespace App\Http\Controllers;

use App\Models\AvailableDate;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('catalog', $request->filled('menu_item') ? [
            'order' => $request->integer('menu_item'),
        ] : []);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedOrderDetails($request, [
            'menu_item_id' => ['required', Rule::exists('menu_items', 'id')->where('is_active', true)],
        ]);

        $menuItem = MenuItem::findOrFail($data['menu_item_id']);

        Order::create([
            'order_number' => 'SD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'user_id' => $request->user()->id,
            'menu_item_id' => $menuItem->id,
            'cake_name' => $menuItem->name,
            'cake_price' => $menuItem->price,
            'delivery_date' => $data['delivery_date'],
            'frosting' => $data['frosting'],
            'toppings' => $data['toppings'] ?? [],
            'phone' => $request->user()->phone,
            'address' => $data['address'],
            'status' => Order::STATUS_PENDING,
        ]);

        return redirect()->route('history')->with('status', 'Order placed successfully.');
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeCustomerPendingOrder($request, $order);

        $data = $this->validatedOrderDetails($request);

        $order->update([
            'delivery_date' => $data['delivery_date'],
            'frosting' => $data['frosting'],
            'toppings' => $data['toppings'] ?? [],
            'address' => $data['address'],
        ]);

        return redirect()->route('history')->with('status', 'Order details updated.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeCustomerPendingOrder($request, $order);

        $order->update([
            'status' => Order::STATUS_CANCELLED,
        ]);

        return redirect()->route('history')->with('status', 'Order cancelled.');
    }

    public function history(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->latest()
            ->get();
        $availableDates = AvailableDate::where('is_available', true)
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->get();

        return view('orders.history', compact('orders', 'availableDates'));
    }

    private function validatedOrderDetails(Request $request, array $extraRules = []): array
    {
        return Validator::make($request->all(), $extraRules + [
            'delivery_date' => ['required', 'date'],
            'frosting' => ['required', 'string', 'max:100'],
            'toppings' => ['array'],
            'toppings.*' => ['string', 'max:100'],
            'address' => ['required', 'string', 'max:1000'],
        ])->after(function ($validator) use ($request): void {
            if (! $request->filled('delivery_date')) {
                return;
            }

            $dateIsAvailable = AvailableDate::whereDate('date', $request->input('delivery_date'))
                ->where('is_available', true)
                ->exists();

            if (! $dateIsAvailable) {
                $validator->errors()->add('delivery_date', 'The selected date is invalid.');
            }
        })->validate();
    }

    private function authorizeCustomerPendingOrder(Request $request, Order $order): void
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_PENDING) {
            abort(403, 'Only pending orders can be changed.');
        }
    }
}
