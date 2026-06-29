@extends('layouts.app')

@section('title', 'Menu - Aifii Qaseh Homemade')

@section('content')
    @php
        $toppingOptions = \App\Support\ToppingOptions::all();
    @endphp

    <section class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <a href="{{ route('home') }}" class="text-sm font-bold uppercase tracking-wide text-rose-600"> < Back to Home</a>
                <h1 class="mt-3 text-4xl font-extrabold text-slate-950">Pick your sweet treat</h1> 
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($menuItems as $cake)
                    <article class="flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-slate-50 shadow-sm sm:flex-row">
                        @if ($cake->image_path)
                            <button type="button" data-image-preview data-image-src="{{ asset($cake->image_path) }}" data-image-alt="{{ $cake->name }}" class="h-44 w-full shrink-0 overflow-hidden bg-slate-200 sm:h-auto sm:w-36">
                                <img src="{{ asset($cake->image_path) }}" alt="{{ $cake->name }}" class="h-full w-full object-cover transition hover:scale-105">
                            </button>
                        @else
                            <div class="flex h-44 w-full shrink-0 items-center justify-center bg-rose-100 px-3 text-center text-xs font-bold uppercase text-rose-700 sm:h-auto sm:w-36">
                                Homemade
                            </div>
                        @endif

                        <div class="flex min-w-0 flex-1 flex-col p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold uppercase text-rose-700">{{ $cake->category }}</span>
                                <h2 class="mt-4 text-xl font-bold text-slate-950">{{ $cake->name }}</h2>
                            </div>
                            <p class="whitespace-nowrap font-bold text-rose-600">RM{{ $cake->price }}</p>
                        </div>

                        <p class="mt-3 flex-1 text-slate-600">{{ $cake->description }}</p>

                        <div class="mt-6 flex justify-end">
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

    <div id="imagePreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 px-4 py-6">
        <button type="button" id="closeImagePreviewBackdrop" class="absolute inset-0" aria-label="Close image preview"></button>
        <div class="relative z-10 max-h-full w-full max-w-4xl">
            <button type="button" id="closeImagePreview" class="absolute right-3 top-3 z-20 rounded-md bg-white px-3 py-1 text-sm font-bold text-slate-950 shadow hover:bg-slate-100">Close</button>
            <img id="imagePreviewContent" src="" alt="" class="mx-auto max-h-[85vh] w-auto rounded-lg bg-white object-contain shadow-2xl">
        </div>
    </div>

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
                                    <span class="text-sm font-semibold text-slate-700">Topping Deco</span>
                                    <select name="frosting" id="modalToppingSelect" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                                        @foreach ($toppingOptions as $frosting => $price)
                                            <option value="{{ $frosting }}" data-price="{{ $price }}" @selected(old('frosting') === $frosting)>{{ $frosting }}</option>
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

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Delivery Address</span>
                                <textarea name="address" rows="4" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none" placeholder="Where should we deliver the cake?">{{ old('address') }}</textarea>
                            </label>

                            <!-- ADDED: Special Remarks Field -->
                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Special Remarks</span>
                                <textarea name="remark" rows="3" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none placeholder-slate-400" placeholder="e.g., Please write 'Happy Birthday' on the cake">{{ old('remark') }}</textarea>
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
                const modalToppingSelect = document.getElementById('modalToppingSelect');
                const orderButtons = document.querySelectorAll('[data-order-button]');
                const autoOpenMenuItemId = @json((string) old('menu_item_id', request('order', '')));
                let selectedCakeBasePrice = 0;

                function updateModalCakePrice() {
                    const toppingPrice = Number(modalToppingSelect?.selectedOptions[0]?.dataset.price || 0);
                    modalCakePrice.textContent = `RM${(selectedCakeBasePrice + toppingPrice).toFixed(2)}`;
                }

                function openOrderModal(button) {
                    modalMenuItemId.value = button.dataset.id;
                    modalCakeName.textContent = button.dataset.name;
                    selectedCakeBasePrice = Number(button.dataset.price);
                    updateModalCakePrice();
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
                modalToppingSelect?.addEventListener('change', updateModalCakePrice);
                orderModal?.addEventListener('click', (event) => {
                    if (event.target === orderModal) {
                        closeOrderModal();
                    }
                });
            </script>
        @endif
    @endauth

    <script>
        const imagePreviewModal = document.getElementById('imagePreviewModal');
        const imagePreviewContent = document.getElementById('imagePreviewContent');
        const imagePreviewButtons = document.querySelectorAll('[data-image-preview]');
        const closeImagePreviewButton = document.getElementById('closeImagePreview');
        const closeImagePreviewBackdrop = document.getElementById('closeImagePreviewBackdrop');

        function openImagePreview(button) {
            if (! imagePreviewModal || ! imagePreviewContent) {
                return;
            }

            imagePreviewContent.src = button.dataset.imageSrc;
            imagePreviewContent.alt = button.dataset.imageAlt;
            imagePreviewModal.classList.remove('hidden');
            imagePreviewModal.classList.add('flex');
        }

        function closeImagePreview() {
            imagePreviewModal?.classList.add('hidden');
            imagePreviewModal?.classList.remove('flex');

            if (imagePreviewContent) {
                imagePreviewContent.src = '';
                imagePreviewContent.alt = '';
            }
        }

        imagePreviewButtons.forEach((button) => {
            button.addEventListener('click', () => openImagePreview(button));
        });
        closeImagePreviewButton?.addEventListener('click', closeImagePreview);
        closeImagePreviewBackdrop?.addEventListener('click', closeImagePreview);
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeImagePreview();
            }
        });
    </script>
@endsection
