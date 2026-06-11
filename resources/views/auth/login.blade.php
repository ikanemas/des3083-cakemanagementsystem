@extends('layouts.app')

@section('title', 'Login - Aifii Qaseh Homemade')

@section('content')
    <main class="bg-white py-12">
        <div class="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Account Login</p>
                <h1 class="mt-3 text-3xl font-extrabold text-slate-950">Login with phone number</h1>
                <p class="mt-3 text-slate-600">Customers log in with their registered phone number.</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5 rounded-lg border border-slate-200 bg-slate-50 p-6">
                @csrf
                @if ($redirect)
                    <input type="hidden" name="redirect" value="{{ $redirect }}">
                @endif

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Phone Number</span>
                    <input name="phone" value="{{ old('phone') }}" required autofocus class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none" placeholder="012-3456789">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Password</span>
                    <input name="password" type="password" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                </label>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input name="remember" type="checkbox" value="1" class="rounded border-slate-300 text-rose-600">
                    Remember me
                </label>

                <button class="w-full rounded-md bg-rose-600 px-4 py-3 font-semibold text-white hover:bg-rose-700">Login</button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-600">
                No account yet?
                <a href="{{ route('register') }}" class="font-semibold text-rose-600 hover:text-rose-700">Register as customer</a>
            </p>

            <div class="mt-4 text-center">
                <a href="{{ route('admin.login') }}" class="text-sm font-semibold text-slate-700 hover:text-rose-600">Login as Admin</a>
            </div>
        </div>
    </main>
@endsection
