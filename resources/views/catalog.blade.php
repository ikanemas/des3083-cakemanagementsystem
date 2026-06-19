@extends('layouts.app')

@section('title', 'Menu - Aifii Qaseh Homemade')

@section('content')
    <section class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-bold uppercase tracking-wide text-rose-600"> Menu</p> 
                <h1 class="mt-3 text-4xl font-extrabold text-slate-950">Choose your perfect cake</h1> 
                <p class="mt-4 text-lg text-slate-600">
                    Anyone can browse the menu. To place an order or view purchase history, please log in as a customer.
                </p> 
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($menuItems as $cake)
                    <article class="flex flex-col rounded-lg border border-slate-200 bg-slate-50 p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold uppercase text-rose-700">{{ $cake->category }}</span>
                                <h2 class="mt-4 text-xl font-bold text-slate-950">{{ $cake->name }}</h2>
                            </div>
                            <p class="whitespace-nowrap font-bold text-rose-600">${{ $cake->price }}</p>
                        </div>

                        <p class="mt-3 flex-1 text-slate-600">{{ $cake->description }}</p>

                        <div class="mt-6 flex items-center justify-between gap-4">
                            <span class="text-sm font-semibold text-slate-500">{{ $cake->serves ?: 'Custom size' }}</span>
                            @auth
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.menu.index') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Manage</a>
                                @else
                                    <button
                                        type="button"
                                        class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                                        data-order-button
                                        data-id="{{ $cake->id }}"
                                        data-name="{{ $cake->name }}"
                                        data-price="{{ $cake->price }}"
                                        @disabled($availableDates->isEmpty())
                                    >
                                        Place Order
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login', ['redirect' => route('catalog', ['order' => $cake->id])]) }}" class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Login to Order</a>
                            @endauth
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 text-slate-600 md:col-span-2 lg:col-span-3">
                        No menu items are available yet.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @auth
        @if (! auth()->user()->isAdmin())
            <div id="orderModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
                <div class="max-h-full w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Place Order</p>
                            <h2 id="modalCakeName" class="mt-1 text-2xl font-extrabold text-slate-950">Cake order</h2>
                            <p id="modalCakePrice" class="mt-1 text-slate-600"></p>
                        </div>
                        <button type="button" id="closeOrderModal" class="rounded-md border border-slate-300 px-3 py-1 text-sm font-semibold text-slate-700 hover:bg-slate-100">Close</button>
                    </div>

                    @if ($availableDates->isEmpty())
                        <div class="px-6 py-6">
                            <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900">
                                No available dates have been selected by admin yet.
                            </div>
                        </div>
                    @else
                        @php
                            $selectedDeliveryDate = old('delivery_date', $availableDates->first()->date->toDateString());
                        @endphp

                        <form method="POST" action="{{ route('order.store') }}" class="space-y-5 px-6 py-6">
                            @csrf
                            <input type="hidden" name="menu_item_id" id="modalMenuItemId" value="{{ old('menu_item_id') }}">

                            <div class="grid gap-5">
                                <div>
                                    <span class="text-sm font-semibold text-slate-700">Available Date</span>
                                    <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                        @foreach ($availableDates as $availableDate)
                                            @php
                                                $dateKey = $availableDate->date->toDateString();
                                            @endphp
                                            <label class="cursor-pointer">
                                                <input type="radio" name="delivery_date" value="{{ $dateKey }}" required @checked($selectedDeliveryDate === $dateKey) class="peer sr-only">
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
                                            <option @selected(old('frosting') === $frosting)>{{ $frosting }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            </div>

                                <label class="block">
                                    <span class="text-sm font-semibold text-slate-700">Topping Deco</span>
                                    <select name="frosting" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                                        @foreach (['Chocolate Flakes (+RM2)', 'Chocolate Ball (+RM2)', 'Kitkat Ball (+RM3)', 'Kitkat Bar (+RM3)', 'Kinder Bueno (+RM5)', 'M&M (+RM3)', 'Oreo Crunch (+RM3)', 'Almond (+RM4)'] as $frosting)
                                            <option @selected(old('frosting') === $frosting)>{{ $frosting }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            <div>
                                <span class="text-sm font-semibold text-slate-700">Contact</span>
                                <div class="mt-1 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-slate-600">
                                    {{ auth()->user()->name }} - {{ auth()->user()->phone }}
                                </div>
                            </div>

                            <fieldset>
                                <legend class="text-sm font-semibold text-slate-700">Fruit Toppings</legend>
                                <div class="mt-2 grid gap-3 sm:grid-cols-3">
                                    @foreach (['Strawberries', 'Mango', 'Blueberries'] as $topping)
                                        <label class="flex items-center rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <input type="checkbox" name="toppings[]" value="{{ $topping }}" @checked(in_array($topping, old('toppings', []), true)) class="mr-2 rounded border-slate-300 text-rose-600">
                                            {{ $topping }}
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Delivery Address</span>
                                <textarea name="address" rows="4" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none" placeholder="Where should we deliver the cake?">{{ old('address') }}</textarea>
                            </label>

                            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                                <button type="button" id="cancelOrderModal" class="rounded-md border border-slate-300 px-5 py-2 font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                                <button class="rounded-md bg-rose-600 px-5 py-2 font-semibold text-white hover:bg-rose-700">Submit Order</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <script>
                const orderModal = document.getElementById('orderModal');
                const modalMenuItemId = document.getElementById('modalMenuItemId');
                const modalCakeName = document.getElementById('modalCakeName');
                const modalCakePrice = document.getElementById('modalCakePrice');
                const orderButtons = document.querySelectorAll('[data-order-button]');
                const autoOpenMenuItemId = @json((string) old('menu_item_id', request('order', '')));

                function openOrderModal(button) {
                    modalMenuItemId.value = button.dataset.id;
                    modalCakeName.textContent = button.dataset.name;
                    modalCakePrice.textContent = `$${Number(button.dataset.price).toFixed(2)}`;
                    orderModal.classList.remove('hidden');
                    orderModal.classList.add('flex');
                }

                function closeOrderModal() {
                    orderModal.classList.add('hidden');
                    orderModal.classList.remove('flex');
                }

                orderButtons.forEach((button) => {
                    button.addEventListener('click', () => openOrderModal(button));

                    if (autoOpenMenuItemId && button.dataset.id === autoOpenMenuItemId) {
                        openOrderModal(button);
                    }
                });

                document.getElementById('closeOrderModal')?.addEventListener('click', closeOrderModal);
                document.getElementById('cancelOrderModal')?.addEventListener('click', closeOrderModal);
                orderModal?.addEventListener('click', (event) => {
                    if (event.target === orderModal) {
                        closeOrderModal();
                    }
                });
            </script>
        @endif
    @endauth
@endsection
