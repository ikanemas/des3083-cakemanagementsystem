@extends('layouts.app')

@section('title', 'Available Dates - Aifii Qaseh Homemade')

@section('content')
    <main class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Admin</p>
                <h1 class="mt-3 text-4xl font-extrabold text-slate-950">Select Available Dates</h1>
            </div>

            <div class="mt-8 flex flex-col gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-slate-500">Calendar Window</p>
                    <p class="mt-1 text-lg font-extrabold text-slate-950">
                        {{ $calendarMonths->first()['month']->format('F Y') }} - {{ $calendarMonths->last()['month']->format('F Y') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    @if ($monthOffset > 0)
                        <a href="{{ route('admin.dates.index', ['month' => max(0, $monthOffset - 3)]) }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-300 bg-white text-xl font-bold text-slate-700 hover:bg-slate-100" aria-label="Previous months">
                            &larr;
                        </a>
                    @else
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-xl font-bold text-slate-300" aria-hidden="true">
                            &larr;
                        </span>
                    @endif

                    <a href="{{ route('admin.dates.index', ['month' => $monthOffset + 3]) }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-300 bg-white text-xl font-bold text-slate-700 hover:bg-slate-100" aria-label="Next months">
                        &rarr;
                    </a>
                </div>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-3">
                @foreach ($calendarMonths as $calendar)
                    <section class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <h2 class="text-center text-xl font-extrabold text-slate-950">{{ $calendar['month']->format('F Y') }}</h2>

                        <div class="mt-5 grid grid-cols-7 gap-2 text-center text-xs font-bold uppercase text-slate-500">
                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                                <div>{{ $weekday }}</div>
                            @endforeach
                        </div>

                        <div class="mt-3 grid grid-cols-7 gap-2">
                            @foreach ($calendar['days'] as $day)
                                @php
                                    $dateKey = $day->toDateString();
                                    $isCurrentMonth = $day->isSameMonth($calendar['month']);
                                    $isPast = $day->isPast() && ! $day->isToday();
                                    $isAvailable = $availableDateKeys->has($dateKey);
                                @endphp

                                @if ($isPast || ! $isCurrentMonth)
                                    <div class="flex aspect-square items-center justify-center rounded-full text-sm {{ $isCurrentMonth ? 'text-slate-300' : 'text-slate-200' }}">
                                        {{ $day->day }}
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('admin.dates.toggle') }}" class="aspect-square">
                                        @csrf
                                        <input type="hidden" name="date" value="{{ $dateKey }}">
                                        <button
                                            class="flex h-full w-full items-center justify-center rounded-full text-sm font-bold transition {{ $isAvailable ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-200 hover:bg-emerald-600' : 'text-slate-700 hover:bg-rose-100 hover:text-rose-700' }}"
                                            title="{{ $isAvailable ? 'Available' : 'Unavailable' }} - {{ $day->format('d M Y') }}"
                                        >
                                            {{ $day->day }}
                                        </button>
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="mt-8 rounded-lg border border-slate-200 bg-slate-50 p-5">
                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2"><span class="h-4 w-4 rounded-full bg-emerald-500"></span>Available to customers</span>
                    <span class="inline-flex items-center gap-2"><span class="h-4 w-4 rounded-full border border-slate-300 bg-white"></span>Admin can click from today onward</span>
                    <span class="inline-flex items-center gap-2"><span class="h-4 w-4 rounded-full bg-slate-100"></span>Past dates cannot be selected</span>
                </div>
            </div>
        </div>
    </main>
@endsection
