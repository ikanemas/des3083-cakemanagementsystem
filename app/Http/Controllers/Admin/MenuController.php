<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menuItems = MenuItem::latest()->get();

        return view('admin.menu.index', compact('menuItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        MenuItem::create($this->validatedData($request));

        return back()->with('status', 'Menu item added.');
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update($this->validatedData($request));

        return back()->with('status', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();

        return back()->with('status', 'Menu item removed.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'serves' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
