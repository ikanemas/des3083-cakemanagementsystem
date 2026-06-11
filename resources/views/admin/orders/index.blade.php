@extends('layouts.app')

@section('title', 'Manage Orders - Aifii Qaseh Homemade')

@section('content')
    <main class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Admin</p>
                <h1 class="mt-3 text-4xl font-extrabold text-slate-950">Manage Orders</h1>
                <p class="mt-4 text-lg text-slate-600">Orders are grouped by status. Click an order row to expand details and update remarks or status.</p>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-2">
                @foreach ($statuses as $statusKey => $statusLabel)
                    @php
                        $statusOrders = $orders->where('status', $statusKey);
                        $badgeClass = match ($statusKey) {
                            'pending' => 'bg-amber-100 text-amber-900',
                            'confirmed' => 'bg-cyan-100 text-cyan-900',
                            'completed' => 'bg-emerald-100 text-emerald-900',
                            'cancelled' => 'bg-red-100 text-red-900',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp

                    <section class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-xl font-extrabold text-slate-950">{{ $statusLabel }}</h2>
                            <span class="rounded-full px-3 py-1 text-sm font-bold {{ $badgeClass }}">{{ $statusOrders->count() }}</span>
                        </div>

                        <div class="mt-5 space-y-3">
                            @forelse ($statusOrders as $order)
                                <details class="group rounded-md border border-slate-200 bg-white">
                                    <summary class="grid cursor-pointer list-none gap-3 px-4 py-4 sm:grid-cols-[1fr_auto] sm:items-center">
                                        <div>
                                            <p class="text-sm font-bold uppercase text-rose-600">{{ $order->order_number }}</p>
                                            <h3 class="mt-1 font-bold text-slate-950">{{ $order->cake_name }}</h3>
                                            <p class="mt-1 text-sm text-slate-600">{{ $order->user->name }} - {{ $order->delivery_date->format('d M Y') }}</p>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm font-semibold text-slate-500">
                                            <span>${{ $order->cake_price }}</span>
                                            <span class="text-slate-400 group-open:hidden">Expand</span>
                                            <span class="hidden text-slate-400 group-open:inline">Collapse</span>
                                        </div>
                                    </summary>

                                    <div class="border-t border-slate-200 px-4 py-4">
                                        <dl class="grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                                            <div><dt class="font-bold text-slate-900">Customer</dt><dd>{{ $order->user->name }}</dd></div>
                                            <div><dt class="font-bold text-slate-900">Phone</dt><dd>{{ $order->phone }}</dd></div>
                                            <div><dt class="font-bold text-slate-900">Delivery Date</dt><dd>{{ $order->delivery_date->format('d M Y') }}</dd></div>
                                            <div><dt class="font-bold text-slate-900">Frosting</dt><dd>{{ $order->frosting }}</dd></div>
                                            <div><dt class="font-bold text-slate-900">Fruit Toppings</dt><dd>{{ count($order->toppings ?? []) ? implode(', ', $order->toppings) : 'None' }}</dd></div>
                                            <div><dt class="font-bold text-slate-900">Placed At</dt><dd>{{ $order->created_at->format('d M Y, h:i A') }}</dd></div>
                                            <div class="sm:col-span-2"><dt class="font-bold text-slate-900">Address</dt><dd>{{ $order->address }}</dd></div>
                                        </dl>

                                        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-5 grid gap-4 rounded-md border border-slate-200 bg-slate-50 p-4">
                                            @csrf
                                            @method('PUT')

                                            <label class="block">
                                                <span class="text-sm font-semibold text-slate-700">Remark</span>
                                                <textarea name="remark" rows="3" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">{{ old('remark', $order->remark) }}</textarea>
                                            </label>

                                            <div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                                                <label class="block">
                                                    <span class="text-sm font-semibold text-slate-700">Order Status</span>
                                                    <select name="status" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                                                        @foreach ($statuses as $value => $label)
                                                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>

                                                <button class="rounded-md bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-700">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            @empty
                                <div class="rounded-md border border-dashed border-slate-300 bg-white px-4 py-6 text-sm text-slate-500">
                                    No {{ strtolower($statusLabel) }} orders.
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </main>
@endsection
