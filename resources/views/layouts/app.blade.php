<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AIFII QASEH HOMEMADE')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800">
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-rose-600">AIFII QASEH HOMEMADE</a>

            <div class="hidden items-center gap-6 md:flex">
                <a href="{{ route('home') }}" class="font-medium text-slate-600 hover:text-rose-600">Home</a>
                <a href="{{ route('catalog') }}" class="font-medium text-slate-600 hover:text-rose-600">Menu</a>

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.orders.index') }}" class="font-medium text-slate-600 hover:text-rose-600">Orders</a>
                        <a href="{{ route('admin.dates.index') }}" class="font-medium text-slate-600 hover:text-rose-600">Dates</a>
                        <a href="{{ route('admin.menu.index') }}" class="font-medium text-slate-600 hover:text-rose-600">Manage Menu</a>
                    @else
                        <a href="{{ route('history') }}" class="font-medium text-slate-600 hover:text-rose-600">History</a>
                    @endif
                @endauth
            </div>

            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Login</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Register</a>
                @else
                    <span class="hidden text-sm text-slate-500 sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    @if (session('status'))
        <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    @yield('content')

    <footer class="mt-16 bg-slate-900">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-slate-400">
                &copy; {{ date('Y') }} Aifii Qaseh Homemade. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
