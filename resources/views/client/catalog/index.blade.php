@extends('layouts.app', ['title' => 'Catalogue Burgers'])

@section('content')
    <section class="mb-6 rounded-xl bg-white p-5 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Catalogue client</h1>
        <p class="mt-1 text-sm text-slate-600">Passez une commande sans compte client, avec votre nom et numero de telephone.</p>
    </section>

    <section class="mb-6 rounded-xl bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Filtrer le catalogue</h2>
        <form method="GET" action="{{ route('catalog.index') }}" class="mt-4 grid gap-3 md:grid-cols-4">
            <input
                type="text"
                name="search"
                value="{{ $filters['search'] ?? '' }}"
                placeholder="Nom du burger"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
            >
            <input
                type="number"
                step="0.01"
                min="0"
                name="price_min"
                value="{{ $filters['price_min'] ?? '' }}"
                placeholder="Prix min"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
            >
            <input
                type="number"
                step="0.01"
                min="0"
                name="price_max"
                value="{{ $filters['price_max'] ?? '' }}"
                placeholder="Prix max"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
            >
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Appliquer</button>
        </form>
    </section>

    <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
        @csrf

        <section class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Informations client</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nom du client</label>
                    <input
                        type="text"
                        name="client_name"
                        value="{{ old('client_name') }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
                    >
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Numero de telephone</label>
                    <input
                        type="text"
                        name="client_phone"
                        value="{{ old('client_phone') }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
                    >
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($burgers as $burger)
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $burger->category->name }}</p>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $burger->name }}</h3>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $burger->stock_quantity > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $burger->stock_quantity > 0 ? 'Disponible' : 'Rupture' }}
                        </span>
                    </div>

                    <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $burger->description ?: 'Sans description.' }}</p>

                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-base font-bold text-orange-600">{{ number_format((float) $burger->price, 0, ',', ' ') }} FCFA</p>
                        <a href="{{ route('catalog.show', $burger) }}" class="text-sm font-semibold text-slate-700 hover:text-orange-600">Details</a>
                    </div>

                    <div class="mt-4">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Quantite</label>
                        <input
                            type="number"
                            min="0"
                            max="{{ $burger->stock_quantity }}"
                            value="{{ old('items.'.$burger->id, 0) }}"
                            name="items[{{ $burger->id }}]"
                            {{ $burger->stock_quantity === 0 ? 'disabled' : '' }}
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none disabled:cursor-not-allowed disabled:bg-slate-100"
                        >
                        <p class="mt-1 text-xs text-slate-500">Stock actuel: {{ $burger->stock_quantity }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-xl bg-white p-6 text-sm text-slate-600 shadow-sm md:col-span-2 xl:col-span-3">
                    Aucun burger ne correspond a vos filtres.
                </div>
            @endforelse
        </section>

        <div class="flex items-center justify-between rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-600">Selectionnez les quantites puis validez.</p>
            <button class="rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-orange-500">Valider la commande</button>
        </div>
    </form>

    <div class="mt-6">{{ $burgers->links() }}</div>
@endsection
