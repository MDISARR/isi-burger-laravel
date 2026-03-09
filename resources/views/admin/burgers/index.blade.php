@extends('layouts.app', ['title' => 'Gestion Burgers'])

@section('content')
    <section class="rounded-xl bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Gestion des burgers</h1>
                <p class="mt-1 text-sm text-slate-600">Ajout, modification, archivage et suppression des produits.</p>
            </div>
            <a href="{{ route('admin.burgers.create') }}" class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-500">
                Ajouter un burger
            </a>
        </div>

        <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-slate-700">Nom</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Categorie</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Prix</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Stock</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Etat</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($burgers as $burger)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $burger->name }}</td>
                            <td class="px-4 py-3">{{ $burger->category->name }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $burger->price, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3">{{ $burger->stock_quantity }}</td>
                            <td class="px-4 py-3">
                                @if ($burger->is_archived)
                                    <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">Archive</span>
                                @elseif ($burger->stock_quantity > 0)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Disponible</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Rupture</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.burgers.edit', $burger) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Modifier</a>

                                    @if ($burger->is_archived)
                                        <form method="POST" action="{{ route('admin.burgers.restore', $burger) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-md border border-emerald-300 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">Restaurer</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.burgers.destroy', $burger) }}" onsubmit="return confirm('Confirmer cette action ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                                            {{ $burger->order_items_exists ? 'Archiver' : 'Supprimer' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">Aucun burger enregistre.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $burgers->links() }}</div>
    </section>
@endsection
