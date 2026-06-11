@extends('layouts.app')

@section('title', 'Aifii Qaseh Homemade - Cake Ordering System')

@section('content')
    <section class="bg-rose-50">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20">
            <div class="flex flex-col justify-center">
                <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Fresh celebration cakes</p>
                <h1 class="mt-4 text-4xl font-extrabold text-slate-950 sm:text-5xl">
                    Delicious cakes for every special occasion
                </h1>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('catalog') }}" class="rounded-md bg-rose-600 px-6 py-3 font-semibold text-white hover:bg-rose-700">Browse Menu</a> 
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Freshly baked</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-950">Peoples' Favorites</h2>
                </div>
                <a href="{{ route('catalog') }}" class="font-semibold text-rose-600 hover:text-rose-700">View full menu</a>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @forelse ($favorites as $cake)
                    <article class="rounded-lg border border-slate-200 bg-slate-50 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold uppercase text-rose-700">{{ $cake->category }}</span>
                                <h3 class="mt-4 text-xl font-bold text-slate-950">{{ $cake->name }}</h3>
                            </div>
                            <p class="font-bold text-rose-600">${{ $cake->price }}</p>
                        </div>
                        <p class="mt-3 text-slate-600">{{ $cake->description }}</p>
                    </article>
                @empty
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-6 text-slate-600 md:col-span-3">
                        The menu is being prepared. Please check back soon.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
