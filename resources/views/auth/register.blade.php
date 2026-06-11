@extends('layouts.app')

@section('title', 'Register - Aifii Qaseh Homemade')

@section('content')
    <main class="bg-white py-12">
        <div class="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Customer Registration</p>
                <h1 class="mt-3 text-3xl font-extrabold text-slate-950">Create your customer account</h1>
                <p class="mt-3 text-slate-600">Register using your name and phone number, then place orders and view purchase history.</p>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5 rounded-lg border border-slate-200 bg-slate-50 p-6">
                @csrf
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Name</span>
                    <input name="name" value="{{ old('name') }}" required autofocus class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none" placeholder="Your name">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Phone Number</span>
                    <input name="phone" value="{{ old('phone') }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none" placeholder="012-3456789">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Password</span>
                    <input name="password" type="password" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Confirm Password</span>
                    <input name="password_confirmation" type="password" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                </label>

                <button class="w-full rounded-md bg-rose-600 px-4 py-3 font-semibold text-white hover:bg-rose-700">Register</button>
            </form>
        </div>
    </main>
@endsection
