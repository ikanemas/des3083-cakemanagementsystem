<label class="block">
    <span class="text-sm font-semibold text-slate-700">Name</span>
    <input name="name" value="{{ old('name', optional($menuItem)->name) }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
</label>

<label class="block">
    <span class="text-sm font-semibold text-slate-700">Category</span>
    <input name="category" value="{{ old('category', optional($menuItem)->category ?? 'Cake') }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
</label>

<label class="block">
    <span class="text-sm font-semibold text-slate-700">Price</span>
    <input name="price" type="number" min="0" step="0.01" value="{{ old('price', optional($menuItem)->price) }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
</label>

<label class="block lg:col-span-2">
    <span class="text-sm font-semibold text-slate-700">Image</span>
    <input name="image" type="file" accept="image/png,image/jpeg,image/webp" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-slate-700 focus:border-rose-500 focus:outline-none">
    @if (optional($menuItem)->image_path)
        <div class="mt-3 flex items-center gap-3 rounded-md border border-slate-200 bg-slate-50 p-3">
            <img src="{{ asset($menuItem->image_path) }}" alt="{{ $menuItem->name }}" class="h-16 w-16 rounded-md object-cover">
            <p class="text-sm font-medium text-slate-600">Upload a new image to replace the current one.</p>
        </div>
    @endif
</label>

<label class="block lg:col-span-2">
    <span class="text-sm font-semibold text-slate-700">Description</span>
    <textarea name="description" rows="3" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">{{ old('description', optional($menuItem)->description) }}</textarea>
</label>

<div class="lg:col-span-2">
    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', optional($menuItem)->is_active ?? true)) class="rounded border-slate-300 text-rose-600">
        Available on public menu
    </label>
</div>
