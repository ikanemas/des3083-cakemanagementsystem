@extends('layouts.app')

@section('title', 'Aifii Qaseh Homemade - Cake Ordering System')

@section('content')
    <style>
        @keyframes heroBackgroundCarousel {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .hero-bg-track {
            animation: heroBackgroundCarousel 28s linear infinite;
        }

        .hero-bg-track img {
            flex: 0 0 auto;
        }

        .favorite-slider {
            scrollbar-width: none;
        }

        .favorite-slider::-webkit-scrollbar {
            display: none;
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-bg-track {
                animation: none;
            }
        }
    </style>

    <section class="relative min-h-screen overflow-hidden bg-black text-white">
        <div class="absolute inset-0 overflow-hidden">
            <div class="hero-bg-track absolute bottom-0 left-[45%] flex h-[140%] w-max items-end">
                <img src="{{ asset('images/home/bg-hero-section.jpeg') }}" alt="" class="h-full w-auto max-w-none object-contain" aria-hidden="true">
                <img src="{{ asset('images/home/slide1.jpeg') }}" alt="" class="h-full w-auto max-w-none object-contain" aria-hidden="true">
                <img src="{{ asset('images/home/slide2.jpeg') }}" alt="" class="h-full w-auto max-w-none object-contain" aria-hidden="true">
                <img src="{{ asset('images/home/slide3.jpeg') }}" alt="" class="h-full w-auto max-w-none object-contain" aria-hidden="true">
                <img src="{{ asset('images/home/bg-hero-section.jpeg') }}" alt="" class="h-full w-auto max-w-none object-contain" aria-hidden="true">
                <img src="{{ asset('images/home/slide1.jpeg') }}" alt="" class="h-full w-auto max-w-none object-contain" aria-hidden="true">
                <img src="{{ asset('images/home/slide2.jpeg') }}" alt="" class="h-full w-auto max-w-none object-contain" aria-hidden="true">
                <img src="{{ asset('images/home/slide3.jpeg') }}" alt="" class="h-full w-auto max-w-none object-contain" aria-hidden="true">
            </div>
        </div>
        <div class="absolute inset-0 bg-[linear-gradient(90deg,#030303_0%,#030303_55%,rgba(3,3,3,0.86)_65%,rgba(3,3,3,0.34)_85%,rgba(3,3,3,0.06)_100%)]"></div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <button type="button" data-open-home-nav aria-label="Open navigation" class="flex h-11 w-11 flex-col items-center justify-center gap-1.5 rounded-md border border-white/25 bg-black/25 hover:bg-white hover:text-slate-950">
                    <span class="h-0.5 w-5 rounded-full bg-current"></span>
                    <span class="h-0.5 w-5 rounded-full bg-current"></span>
                    <span class="h-0.5 w-5 rounded-full bg-current"></span>
                </button>

                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="rounded-md border border-white/35 px-4 py-2 text-sm font-semibold text-white hover:bg-white hover:text-slate-950">Login</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">Register</a>
                    @else
                        <span class="hidden text-sm font-medium text-white/75 sm:inline">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-md border border-white/35 px-4 py-2 text-sm font-semibold text-white hover:bg-white hover:text-slate-950">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>

            <div class="flex flex-1 items-center py-16">
                <div class="max-w-xl">
                    <p class="text-sm font-bold uppercase tracking-wide text-rose-300">Aifii Qaseh Homemade</p>
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                        Baked to perfection for every occasion
                    </h1>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#home-menu" class="rounded-md bg-rose-600 px-6 py-3 font-semibold text-white hover:bg-rose-500">See our best sellers</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div data-home-nav class="fixed inset-0 z-50 hidden">
        <button type="button" data-close-home-nav aria-label="Close navigation backdrop" class="absolute inset-0 bg-slate-950/70"></button>
        <aside class="relative flex h-full w-72 max-w-[85vw] flex-col bg-slate-950 px-5 py-6 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-2xl font-extrabold text-rose-300">AQ</a>
                <button type="button" data-close-home-nav aria-label="Close navigation" class="flex h-10 w-10 items-center justify-center rounded-md border border-white/20 text-2xl leading-none hover:bg-white hover:text-slate-950">&times;</button>
            </div>

            <nav class="mt-10 flex flex-col gap-3 text-sm font-bold uppercase tracking-wide">
                <a href="{{ route('home') }}" class="rounded-md border border-white/15 bg-white/10 px-4 py-3 text-white/85 transition hover:border-white/35 hover:bg-white hover:text-slate-950">Home</a>
                <a href="#home-menu" data-close-home-nav class="rounded-md border border-white/15 bg-white/10 px-4 py-3 text-white/85 transition hover:border-white/35 hover:bg-white hover:text-slate-950">Best Sellers</a>
                <a href="{{ route('catalog') }}" class="rounded-md border border-white/15 bg-white/10 px-4 py-3 text-white/85 transition hover:border-white/35 hover:bg-white hover:text-slate-950">Menu</a>

                @auth
                    @if (auth()->user()->isAdmin())
                        <span class="my-2 h-px bg-white/15"></span>
                        <a href="{{ route('admin.orders.index') }}" class="rounded-md border border-white/15 bg-white/10 px-4 py-3 text-white/85 transition hover:border-white/35 hover:bg-white hover:text-slate-950">Orders</a>
                        <a href="{{ route('admin.menu.index') }}" class="rounded-md border border-white/15 bg-white/10 px-4 py-3 text-white/85 transition hover:border-white/35 hover:bg-white hover:text-slate-950">Admin</a>
                    @endif
                @endauth
            </nav>
        </aside>
    </div>

    <section id="home-menu" class="bg-white py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Freshly baked</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-950">Peoples' Favorites</h2>
                </div>
                <a href="{{ route('catalog') }}" class="font-semibold text-rose-600 hover:text-rose-700">View full menu</a>
            </div>

            <div class="relative mt-8">
                <div data-favorite-slider class="favorite-slider flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth pb-2">
                @forelse ($favorites as $cake)
                    <article class="group relative h-80 min-w-full snap-start overflow-hidden rounded-lg border border-slate-200 bg-slate-900 shadow-sm sm:min-w-[calc(50%-12px)] lg:min-w-[calc(33.333%-16px)]">
                        @if ($cake->image_path)
                            <img src="{{ asset($cake->image_path) }}" alt="{{ $cake->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-rose-100 px-6 text-center text-sm font-bold uppercase tracking-wide text-rose-700">
                                Homemade
                            </div>
                        @endif

                        <div class="absolute inset-x-0 top-0 bg-gradient-to-b from-black/75 to-transparent p-5">
                            <p class="text-xs font-bold uppercase tracking-wide text-rose-200">{{ $cake->category }}</p>
                            <h3 class="mt-2 text-2xl font-extrabold text-white">{{ $cake->name }}</h3>
                        </div>

                        <div class="absolute inset-0 flex translate-y-4 items-end bg-black/65 p-5 opacity-0 transition duration-300 group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:translate-y-0 group-focus-within:opacity-100">
                            <p class="text-base font-medium leading-7 text-white">{{ $cake->description }}</p>
                        </div>
                    </article>
                @empty
                    <div class="min-w-full rounded-lg border border-slate-200 bg-slate-50 p-6 text-slate-600">
                        The menu is being prepared. Please check back soon.
                    </div>
                @endforelse
                </div>
            </div>
        </div>
    </section>

    <script>
        const homeNav = document.querySelector('[data-home-nav]');
        const openHomeNavButton = document.querySelector('[data-open-home-nav]');
        const closeHomeNavButtons = document.querySelectorAll('[data-close-home-nav]');

        function openHomeNav() {
            homeNav?.classList.remove('hidden');
        }

        function closeHomeNav() {
            homeNav?.classList.add('hidden');
        }

        openHomeNavButton?.addEventListener('click', openHomeNav);
        closeHomeNavButtons.forEach((button) => {
            button.addEventListener('click', closeHomeNav);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeHomeNav();
            }
        });
    </script>
@endsection
