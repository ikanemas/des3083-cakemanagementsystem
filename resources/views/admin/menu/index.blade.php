@extends('layouts.app')

@section('title', 'Manage Menu - Aifii Qaseh Homemade')

@section('content')
    <main class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Admin</p>
                    <h1 class="mt-3 text-4xl font-extrabold text-slate-950">Manage Menu</h1>
                    <p class="mt-4 text-lg text-slate-600">View menu items at a glance. Add or edit details from a popup.</p>
                </div>
                <button type="button" data-open-modal="add-menu-modal" class="rounded-md bg-rose-600 px-5 py-3 font-semibold text-white hover:bg-rose-700">Add Menu Item</button>
            </div>

            <div class="mt-8 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                <div class="hidden grid-cols-[1.4fr_1fr_0.7fr_0.8fr_0.8fr] gap-4 border-b border-slate-200 bg-white px-5 py-3 text-sm font-bold uppercase tracking-wide text-slate-500 lg:grid">
                    <div>Name</div>
                    <div>Category</div>
                    <div>Price</div>
                    <div>Status</div>
                    <div class="text-right">Actions</div>
                </div>

                <div class="divide-y divide-slate-200">
                    @forelse ($menuItems as $menuItem)
                        <article class="grid gap-4 bg-slate-50 px-5 py-4 lg:grid-cols-[1.4fr_1fr_0.7fr_0.8fr_0.8fr] lg:items-center">
                            <div>
                                <h2 class="font-bold text-slate-950">{{ $menuItem->name }}</h2>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $menuItem->description }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ $menuItem->serves ?: 'Custom size' }}</p>
                            </div>
                            <div class="text-sm font-semibold text-slate-700">{{ $menuItem->category }}</div>
                            <div class="font-bold text-rose-600">${{ $menuItem->price }}</div>
                            <div>
                                <span class="rounded-full px-3 py-1 text-sm font-bold {{ $menuItem->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $menuItem->is_active ? 'Visible' : 'Hidden' }}
                                </span>
                            </div>
                            <div class="flex flex-wrap justify-start gap-2 lg:justify-end">
                                <button type="button" data-open-modal="edit-menu-modal-{{ $menuItem->id }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Edit</button>
                                <form method="POST" action="{{ route('admin.menu.destroy', $menuItem) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-md border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Remove</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="p-8 text-center text-slate-600">
                            No menu items yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <div id="add-menu-modal" data-menu-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-rose-600">New Menu Item</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-950">Add Cake</h2>
                </div>
                <button type="button" data-close-modal class="rounded-md border border-slate-300 px-3 py-1 text-sm font-semibold text-slate-700 hover:bg-slate-100">Close</button>
            </div>
            <form method="POST" action="{{ route('admin.menu.store') }}" class="grid gap-4 px-6 py-6 lg:grid-cols-2">
                @csrf
                @include('admin.menu.partials.form-fields', ['menuItem' => null])
                <div class="flex justify-end gap-2 lg:col-span-2">
                    <button type="button" data-close-modal class="rounded-md border border-slate-300 px-5 py-2 font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                    <button class="rounded-md bg-rose-600 px-5 py-2 font-semibold text-white hover:bg-rose-700">Add Menu Item</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($menuItems as $menuItem)
        <div id="edit-menu-modal-{{ $menuItem->id }}" data-menu-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
            <div class="max-h-full w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Edit Menu Item</p>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-950">{{ $menuItem->name }}</h2>
                    </div>
                    <button type="button" data-close-modal class="rounded-md border border-slate-300 px-3 py-1 text-sm font-semibold text-slate-700 hover:bg-slate-100">Close</button>
                </div>
                <form method="POST" action="{{ route('admin.menu.update', $menuItem) }}" class="grid gap-4 px-6 py-6 lg:grid-cols-2">
                    @csrf
                    @method('PUT')
                    @include('admin.menu.partials.form-fields', ['menuItem' => $menuItem])
                    <div class="flex justify-end gap-2 lg:col-span-2">
                        <button type="button" data-close-modal class="rounded-md border border-slate-300 px-5 py-2 font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                        <button class="rounded-md bg-slate-900 px-5 py-2 font-semibold text-white hover:bg-slate-700">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script>
        const menuModals = document.querySelectorAll('[data-menu-modal]');

        function closeMenuModals() {
            menuModals.forEach((modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        }

        document.querySelectorAll('[data-open-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                closeMenuModals();
                const modal = document.getElementById(button.dataset.openModal);
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', closeMenuModals);
        });

        menuModals.forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeMenuModals();
                }
            });
        });
    </script>
@endsection
