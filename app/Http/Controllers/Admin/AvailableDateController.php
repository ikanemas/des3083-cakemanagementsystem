<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailableDate;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailableDateController extends Controller
{
    public function index(Request $request): View
    {
        $monthOffset = max(0, $request->integer('month'));
        $availableDates = AvailableDate::orderBy('date')->get();
        $availableDateKeys = $availableDates
            ->filter->is_available
            ->mapWithKeys(fn (AvailableDate $availableDate): array => [
                $availableDate->date->toDateString() => true,
            ]);
        $calendarMonths = collect(range(0, 2))->map(function (int $visibleMonthOffset) use ($monthOffset): array {
            $month = CarbonImmutable::today()->startOfMonth()->addMonths($monthOffset + $visibleMonthOffset);
            $start = $month->startOfMonth()->startOfWeek();
            $end = $month->endOfMonth()->endOfWeek();
            $days = [];

            for ($day = $start; $day->lessThanOrEqualTo($end); $day = $day->addDay()) {
                $days[] = $day;
            }

            return [
                'month' => $month,
                'days' => $days,
            ];
        });

        return view('admin.dates.index', compact('availableDates', 'availableDateKeys', 'calendarMonths', 'monthOffset'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today', 'unique:available_dates,date'],
        ]);

        AvailableDate::create($data + ['is_available' => true]);

        return back()->with('status', 'Available date added.');
    }

    public function toggle(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $availableDate = AvailableDate::whereDate('date', $data['date'])->first();

        if ($availableDate) {
            $availableDate->update([
                'is_available' => ! $availableDate->is_available,
            ]);
        } else {
            AvailableDate::create([
                'date' => $data['date'],
                'is_available' => true,
            ]);
        }

        return back()->with('status', 'Date availability updated.');
    }

    public function update(Request $request, AvailableDate $availableDate): RedirectResponse
    {
        $data = $request->validate([
            'is_available' => ['nullable', 'boolean'],
        ]);

        $availableDate->update([
            'is_available' => (bool) ($data['is_available'] ?? false),
        ]);

        return back()->with('status', 'Date availability updated.');
    }

    public function destroy(AvailableDate $availableDate): RedirectResponse
    {
        $availableDate->delete();

        return back()->with('status', 'Available date removed.');
    }
}
