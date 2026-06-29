<?php

namespace App\Http\Controllers;

use App\Models\AvailableDate;
use App\Models\MenuItem;
use App\Models\Order;
use App\Support\ToppingOptions;
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

        // 1. Double-check that the date hasn't been fully booked in the last few seconds
        $availableDate = AvailableDate::whereDate('date', $data['delivery_date'])->first();
        if (! $availableDate || $availableDate->capacity <= 0 || ! $availableDate->is_available) {
            return back()->withInput()->withErrors(['delivery_date' => 'Sorry, this date just became fully booked!']);
        }

        $menuItem = MenuItem::findOrFail($data['menu_item_id']);
        $cleanCakeName = Str::upper(Str::slug($menuItem->name));

        // 2. Create the order
        Order::create([
            'order_number' => 'SD-'.now()->format('Ymd').'-'.$cleanCakeName.'-'.Str::upper(Str::random(4)),
            'user_id' => $request->user()->id,
            'menu_item_id' => $menuItem->id,
            'cake_name' => $menuItem->name,
            'cake_price' => ToppingOptions::totalPrice($menuItem->price, $data['frosting']),
            'delivery_date' => $data['delivery_date'],
            'frosting' => $data['frosting'],
            'toppings' => $data['toppings'] ?? [],
            'phone' => $request->user()->phone,
            'address' => $data['address'],
            'remark' => $data['remark'] ?? null,
            'status' => Order::STATUS_PENDING,
        ]);

        // 3. Decrease the slot count
        $availableDate->decrement('capacity');

        // 4. Lock the date if capacity reaches 0
        if ($availableDate->capacity <= 0) {
            $availableDate->update(['is_available' => false]);
        }

        return redirect()->route('history')->with('status', 'Order placed successfully.');
    }    
    
    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeCustomerPendingOrder($request, $order);

        $data = $this->validatedOrderDetails($request);

        $order->update([
            'cake_price' => ToppingOptions::totalPrice($order->menuItem?->price ?? $order->cake_price, $data['frosting']),
            'delivery_date' => $data['delivery_date'],
            'frosting' => $data['frosting'],
            'toppings' => $data['toppings'] ?? [],
            'address' => $data['address'],
            'remark' => $data['remark'] ?? null, 
        ]);

        return redirect()->route('history')->with('status', 'Order details updated.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeCustomerPendingOrder($request, $order);

        $order->update([
            'status' => Order::STATUS_CANCELLED,
        ]);

        // Free up the slot for that date
        $availableDate = AvailableDate::whereDate('date', $order->delivery_date)->first();
        if ($availableDate) {
            $availableDate->increment('capacity');
            
            // Unlock the date if it was previously locked
            if (! $availableDate->is_available && $availableDate->capacity > 0) {
                $availableDate->update(['is_available' => true]);
            }
        }

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
            'remark' => ['nullable', 'string', 'max:500'], // <-- Add this line
        ])->after(function ($validator) use ($request): void {
            // ... (keep your existing date validation code here)
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
