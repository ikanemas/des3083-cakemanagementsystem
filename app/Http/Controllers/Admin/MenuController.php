<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
        $data = $this->validatedData($request);
        $data['image_path'] = $this->storeImage($request);

        MenuItem::create($data);

        return back()->with('status', 'Menu item added.');
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($menuItem);
            $data['image_path'] = $this->storeImage($request);
        }

        $menuItem->update($data);

        return back()->with('status', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $this->deleteImage($menuItem);
        $menuItem->delete();

        return back()->with('status', 'Menu item removed.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        unset($validated['image']);

        return $validated + ['is_active' => false];
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $directory = public_path('images/menu');
        File::ensureDirectoryExists($directory);

        $image = $request->file('image');
        $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
        $image->move($directory, $filename);

        return 'images/menu/'.$filename;
    }

    private function deleteImage(MenuItem $menuItem): void
    {
        if (! $menuItem->image_path) {
            return;
        }

        File::delete(public_path($menuItem->image_path));
    }
}
