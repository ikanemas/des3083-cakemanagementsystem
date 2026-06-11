<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        return view('auth.login', ['redirect' => $request->query('redirect')]);
    }

    public function showAdminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'phone' => $data['phone'],
            'password' => $data['password'],
            'role' => 'customer',
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['phone' => 'The phone number or password is incorrect.'])
                ->onlyInput('phone');
        }

        $request->session()->regenerate();

        if ($request->filled('redirect')) {
            return redirect($request->input('redirect'));
        }

        return redirect()->intended(route('catalog'));
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'username' => $data['username'],
            'password' => $data['password'],
            'role' => 'admin',
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'The admin username or password is incorrect.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.orders.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'role' => 'customer',
            'password' => $data['password'],
        ]);

        Auth::login($user);

        return redirect()->route('catalog')->with('status', 'Account created. You can place an order now.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
