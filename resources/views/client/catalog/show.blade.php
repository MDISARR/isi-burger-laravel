@extends('layouts.app', ['title' => $burger->name])

@section('content')
    <section class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $burger->category->name }}</p>
        <h1 class="mt-1 text-3xl font-bold text-slate-900">{{ $burger->name }}</h1>

        <div class="mt-4 grid gap-6 lg:grid-cols-2">
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                @if ($burger->image_path)
                    <img src="{{ asset('storage/'.$burger->image_path) }}" alt="{{ $burger->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-72 items-center justify-center text-sm text-slate-500">Aucune image disponible</div>
                @endif
            </div>
            <div>
                <p class="text-sm text-slate-600">{{ $burger->description ?: 'Pas de description disponible.' }}</p>

                <div class="mt-5 rounded-lg border border-slate-200 p-4">
                    <p class="text-sm text-slate-500">Prix unitaire</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format((float) $burger->price, 0, ',', ' ') }} FCFA</p>
                </div>

                <div class="mt-4 rounded-lg border border-slate-200 p-4">
                    <p class="text-sm text-slate-500">Disponibilite</p>
                    <p class="text-base font-semibold {{ $burger->stock_quantity > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $burger->stock_quantity > 0 ? 'En stock ('.$burger->stock_quantity.')' : 'Rupture de stock' }}
                    </p>
                </div>

                <a href="{{ route('catalog.index') }}" class="mt-6 inline-block rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Retour au catalogue
                </a>
            </div>
        </div>
    </section>
@endsection
