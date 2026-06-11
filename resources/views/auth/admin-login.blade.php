@extends('layouts.app')

@section('title', 'Admin Login - Aifii Qaseh Homemade')

@section('content')
    <main class="bg-white py-12">
        <div class="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-rose-600">Admin Login</p>
                <h1 class="mt-3 text-3xl font-extrabold text-slate-950">Login with admin username</h1>
                <p class="mt-3 text-slate-600">Only admin accounts can access this login.</p>
            </div>

            <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 space-y-5 rounded-lg border border-slate-200 bg-slate-50 p-6">
                @csrf
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Username</span>
                    <input name="username" value="{{ old('username') }}" required autofocus class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none" placeholder="admin">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Password</span>
                    <input name="password" type="password" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
                </label>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input name="remember" type="checkbox" value="1" class="rounded border-slate-300 text-rose-600">
                    Remember me
                </label>

                <button class="w-full rounded-md bg-slate-900 px-4 py-3 font-semibold text-white hover:bg-slate-700">Login as Admin</button>
            </form>

            <div class="mt-5 text-center">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-rose-600 hover:text-rose-700">Back to customer login</a>
            </div>
        </div>
    </main>
@endsection
