@csrf
@if (($method ?? 'POST') !== 'POST')
    @method($method)
@endif

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Nom</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $burger->name) }}"
            required
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
        >
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Categorie</label>
        <select name="category_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none">
            <option value="">Selectionner</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $burger->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Prix unitaire (FCFA)</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $burger->price) }}"
            required
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
        >
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Stock</label>
        <input
            type="number"
            min="0"
            name="stock_quantity"
            value="{{ old('stock_quantity', $burger->stock_quantity ?? 0) }}"
            required
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
        >
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
        <textarea
            name="description"
            rows="4"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
        >{{ old('description', $burger->description) }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-700">Image (optionnel)</label>
        <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        @if ($burger->image_path)
            <p class="mt-1 text-xs text-slate-500">Image actuelle: {{ $burger->image_path }}</p>
        @endif
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-slate-700 md:col-span-2">
        <input type="checkbox" name="is_archived" value="1" @checked(old('is_archived', $burger->is_archived))>
        Archiver ce burger
    </label>
</div>

<div class="mt-5 flex items-center justify-between">
    <a href="{{ route('admin.burgers.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Annuler</a>
    <button class="rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-500">Enregistrer</button>
</div>
