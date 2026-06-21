@extends('layouts.app')

@section('title', 'Place Order - Aifii Qaseh Homemade')

@section('content')
    <main class="bg-white py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Cake Order</p>
                <h1 class="mt-3 text-4xl font-extrabold text-slate-950">Place an order</h1>
                <p class="mt-4 text-lg text-slate-600">
                    Select a cake, choose one of the admin-approved dates, and submit your details.
                </p>
            </div>

            @if ($menuItems->isEmpty())
                <div class="mt-8 rounded-lg border border-slate-200 bg-slate-50 p-8 text-slate-600">
                    No cakes are available for ordering yet.
                </div>
            @elseif ($availableDates->isEmpty())
                <div class="mt-8 rounded-lg border border-amber-200 bg-amber-50 p-8 text-amber-900">
                    No available dates have been selected by admin yet.
                </div>
            @else
                <form method="POST" action="{{ route('order.store') }}" class="mt-8 space-y-6 rounded-lg border border-slate-200 bg-slate-50 p-6">
                    @csrf

                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Cake</span>
                            <select name="menu_item_id" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                                @foreach ($menuItems as $menuItem)
                                    <option value="{{ $menuItem->id }}" @selected(optional($selectedMenuItem)->id === $menuItem->id)>
                                        {{ $menuItem->name }} - RM{{ $menuItem->price }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Available Date</span>
                            <select name="delivery_date" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                                @foreach ($availableDates as $availableDate)
                                    <option value="{{ $availableDate->date->toDateString() }}">
                                        {{ $availableDate->date->format('d M Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Frosting</span>
                            <select name="frosting" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                                <option>No Toppings</option>
                                <option>Vanilla Buttercream</option>
                                <option>Chocolate Ganache</option>
                                <option>Cream Cheese</option>
                                <option>Whipped Cream</option>
                            </select>
                        </label>

                        <div>
                            <span class="text-sm font-semibold text-slate-700">Contact</span>
                            <div class="mt-1 rounded-md border border-slate-200 bg-white px-3 py-2 text-slate-600">
                                {{ auth()->user()->name }} - {{ auth()->user()->phone }}
                            </div>
                        </div>
                    </div>

                    <fieldset>
                        <legend class="text-sm font-semibold text-slate-700">Fruit Toppings</legend>
                        <div class="mt-2 grid gap-3 sm:grid-cols-3">
                            @foreach (['Strawberries', 'Mango', 'Blueberries'] as $topping)
                                <label class="flex items-center rounded-md border border-slate-200 bg-white px-3 py-2">
                                    <input type="checkbox" name="toppings[]" value="{{ $topping }}" class="mr-2 rounded border-slate-300 text-rose-600">
                                    {{ $topping }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Delivery Address</span>
                        <textarea name="address" rows="4" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none" placeholder="Where should we deliver the cake?">{{ old('address') }}</textarea>
                    </label>

                    <div class="flex justify-end">
                        <button class="rounded-md bg-rose-600 px-6 py-3 font-semibold text-white hover:bg-rose-700">Place Order</button>
                    </div>
                </form>
            @endif
        </div>
    </main>
@endsection
