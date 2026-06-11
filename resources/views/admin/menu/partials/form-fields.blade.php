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

<label class="block">
    <span class="text-sm font-semibold text-slate-700">Serves</span>
    <input name="serves" value="{{ old('serves', optional($menuItem)->serves) }}" placeholder="6-8 pax" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-rose-500 focus:outline-none">
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
