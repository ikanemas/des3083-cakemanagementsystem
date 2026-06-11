@extends('layouts.app')

@section('title', 'Purchase History - Aifii Qaseh Homemade')

@section('content')
    <main class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Order Records</p>
                    <h1 class="mt-3 text-4xl font-extrabold text-slate-950">Purchase History</h1>
                    <p class="mt-4 text-lg text-slate-600">Pending orders can be edited or cancelled before admin confirms them.</p>
                </div>
                <a href="{{ route('catalog') }}" class="rounded-md bg-rose-600 px-5 py-3 font-semibold text-white hover:bg-rose-700">Order Again</a>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                @forelse ($orders as $order)
                    @php
                        $isPending = $order->status === \App\Models\Order::STATUS_PENDING;
                        $statusClass = match ($order->status) {
                            'pending' => 'bg-amber-100 text-amber-900',
                            'confirmed' => 'bg-cyan-100 text-cyan-900',
                            'completed' => 'bg-emerald-100 text-emerald-900',
                            'cancelled' => 'bg-red-100 text-red-900',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp

                    <article class="rounded-lg border border-slate-200 bg-slate-50 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold uppercase text-rose-600">{{ $order->order_number }}</p>
                                <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ $order->cake_name }}</h2>
                                <p class="mt-1 text-slate-600">${{ $order->cake_price }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-sm font-bold capitalize {{ $statusClass }}">{{ $order->status }}</span>
                        </div>

                        <dl class="mt-5 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                            <div><dt class="font-bold text-slate-900">Delivery Date</dt><dd>{{ $order->delivery_date->format('d M Y') }}</dd></div>
                            <div><dt class="font-bold text-slate-900">Frosting</dt><dd>{{ $order->frosting }}</dd></div>
                            <div><dt class="font-bold text-slate-900">Fruit Toppings</dt><dd>{{ count($order->toppings ?? []) ? implode(', ', $order->toppings) : 'None' }}</dd></div>
                            <div><dt class="font-bold text-slate-900">Placed At</dt><dd>{{ $order->created_at->format('d M Y, h:i A') }}</dd></div>
                            <div class="sm:col-span-2"><dt class="font-bold text-slate-900">Address</dt><dd>{{ $order->address }}</dd></div>
                            @if ($order->remark)
                                <div class="sm:col-span-2"><dt class="font-bold text-slate-900">Admin Remark</dt><dd>{{ $order->remark }}</dd></div>
                            @endif
                        </dl>

                        @if ($isPending)
                            <div class="mt-5 flex flex-wrap justify-end gap-2">
                                <button type="button" data-open-history-modal="edit-order-modal-{{ $order->id }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Edit Details</button>
                                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order?');">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-md border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Cancel Order</button>
                                </form>
                            </div>
                        @else
                            <p class="mt-5 rounded-md bg-white px-4 py-3 text-sm font-semibold text-slate-500">This order is locked after admin processing.</p>
                        @endif
                    </article>
                @empty
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 text-center lg:col-span-2">
                        <h2 class="text-2xl font-bold text-slate-950">No orders yet</h2>
                        <p class="mt-3 text-slate-600">Once you place an order, it will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    @foreach ($orders->where('status', \App\Models\Order::STATUS_PENDING) as $order)
        <div id="edit-order-modal-{{ $order->id }}" data-history-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
            <div class="max-h-full w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Edit Pending Order</p>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-950">{{ $order->cake_name }}</h2>
                        <p class="mt-1 text-slate-600">{{ $order->order_number }}</p>
                    </div>
                    <button type="button" data-close-history-modal class="rounded-md border border-slate-300 px-3 py-1 text-sm font-semibold text-slate-700 hover:bg-slate-100">Close</button>
                </div>

                @if ($availableDates->isEmpty())
                    <div class="px-6 py-6">
                        <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900">
                            No available dates are currently selected by admin, so this order cannot be edited right now.
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('orders.update', $order) }}" class="space-y-5 px-6 py-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <span class="text-sm font-semibold text-slate-700">Available Date</span>
                            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach ($availableDates as $availableDate)
                                    @php
                                        $dateKey = $availableDate->date->toDateString();
                                    @endphp
                                    <label class="cursor-pointer">
                                        <input type="radio" name="delivery_date" value="{{ $dateKey }}" required @checked($order->delivery_date->toDateString() === $dateKey) class="peer sr-only">
                                        <span class="flex min-h-16 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-center text-sm font-bold text-emerald-800 transition peer-checked:bg-emerald-500 peer-checked:text-white peer-focus:ring-2 peer-focus:ring-emerald-300">
                                            {{ $availableDate->date->format('d M') }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Frosting</span>
                            <select name="frosting" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                                @foreach (['Vanilla Buttercream', 'Chocolate Ganache', 'Cream Cheese', 'Whipped Cream'] as $frosting)
                                    <option @selected($order->frosting === $frosting)>{{ $frosting }}</option>
                                @endforeach
                            </select>
                        </label>

                        <fieldset>
                            <legend class="text-sm font-semibold text-slate-700">Fruit Toppings</legend>
                            <div class="mt-2 grid gap-3 sm:grid-cols-3">
                                @foreach (['Strawberries', 'Mango', 'Blueberries'] as $topping)
                                    <label class="flex items-center rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                        <input type="checkbox" name="toppings[]" value="{{ $topping }}" @checked(in_array($topping, $order->toppings ?? [], true)) class="mr-2 rounded border-slate-300 text-rose-600">
                                        {{ $topping }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Delivery Address</span>
                            <textarea name="address" rows="4" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">{{ $order->address }}</textarea>
                        </label>

                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" data-close-history-modal class="rounded-md border border-slate-300 px-5 py-2 font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                            <button class="rounded-md bg-slate-900 px-5 py-2 font-semibold text-white hover:bg-slate-700">Save Changes</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endforeach

    <script>
        const historyModals = document.querySelectorAll('[data-history-modal]');

        function closeHistoryModals() {
            historyModals.forEach((modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        }

        document.querySelectorAll('[data-open-history-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                closeHistoryModals();
                const modal = document.getElementById(button.dataset.openHistoryModal);
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
            });
        });

        document.querySelectorAll('[data-close-history-modal]').forEach((button) => {
            button.addEventListener('click', closeHistoryModals);
        });

        historyModals.forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeHistoryModals();
                }
            });
        });
    </script>
@endsection
