<?php

namespace App\Http\Controllers;

use App\Models\AvailableDate;
use App\Models\MenuItem;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $favorites = MenuItem::where('is_active', true)->latest()->take(3)->get();

        return view('home', compact('favorites'));
    }

    public function catalog(): View
    {
        $menuItems = MenuItem::where('is_active', true)->latest()->get();
        $availableDates = AvailableDate::where('is_available', true)
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->get();

        return view('catalog', compact('menuItems', 'availableDates'));
    }
}
