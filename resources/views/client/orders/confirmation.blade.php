@extends('layouts.app', ['title' => 'Confirmation commande'])

@section('content')
    <section class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Commande confirmee</h1>
        <p class="mt-1 text-sm text-slate-600">{{ $order->confirmation_message }}</p>

        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Reference</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->reference }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Client</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->client_name }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Telephone</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->client_phone }}</p>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-slate-700">Burger</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Qte</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">PU</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->burger_name }}</td>
                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 font-semibold">{{ number_format((float) $item->line_total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-slate-600">Statut: <span class="font-semibold">{{ $order->statusLabel() }}</span></p>
            <p class="text-lg font-bold text-orange-600">Montant total: {{ number_format((float) $order->total_amount, 0, ',', ' ') }} FCFA</p>
        </div>

        <a href="{{ route('catalog.index') }}" class="mt-6 inline-block rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Nouvelle commande
        </a>
    </section>
@endsection
