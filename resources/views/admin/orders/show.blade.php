@extends('layouts.app', ['title' => 'Detail commande'])

@section('content')
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-xl bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Commande {{ $order->reference }}</h1>
                    <p class="mt-1 text-sm text-slate-600">Creee le {{ $order->placed_at?->format('d/m/Y a H:i') }}</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $order->statusLabel() }}</span>
            </div>

            <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-slate-700">Produit</th>
                            <th class="px-4 py-3 font-semibold text-slate-700">Categorie</th>
                            <th class="px-4 py-3 font-semibold text-slate-700">Qte</th>
                            <th class="px-4 py-3 font-semibold text-slate-700">PU</th>
                            <th class="px-4 py-3 font-semibold text-slate-700">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->burger_name }}</td>
                                <td class="px-4 py-3">{{ $item->burger?->category?->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $item->unit_price, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 font-semibold">{{ number_format((float) $item->line_total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between text-sm">
                <p class="text-slate-600">Montant total</p>
                <p class="text-xl font-bold text-orange-600">{{ number_format((float) $order->total_amount, 0, ',', ' ') }} FCFA</p>
            </div>
        </article>

        <aside class="space-y-4">
            <article class="rounded-xl bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Client</h2>
                <p class="mt-2 text-sm text-slate-700">{{ $order->client_name }}</p>
                <p class="text-sm text-slate-600">{{ $order->client_phone }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $order->confirmation_message }}</p>
            </article>

            <article class="rounded-xl bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Actions</h2>

                @if (!in_array($order->status, ['paid', 'canceled'], true))
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mt-3 space-y-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}" @selected($order->status === $status)>{{ $statusLabels[$status] }}</option>
                            @endforeach
                        </select>
                        <button class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Mettre a jour</button>
                    </form>

                    <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" class="mt-3" onsubmit="return confirm('Annuler cette commande ?');">
                        @csrf
                        @method('PATCH')
                        <button class="w-full rounded-lg border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">Annuler la commande</button>
                    </form>
                @endif

                @if (!$order->payment && $order->status !== 'canceled')
                    <form method="POST" action="{{ route('admin.orders.pay', $order) }}" class="mt-3 space-y-2">
                        @csrf
                        <input type="hidden" name="payment_method" value="especes">
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ (float) $order->total_amount }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Enregistrer le paiement</button>
                    </form>
                @endif

                @if ($order->invoice_path)
                    <a href="{{ route('admin.orders.invoice', $order) }}" class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-indigo-300 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                        Telecharger la facture PDF
                    </a>
                @endif
            </article>

            @if ($order->payment)
                <article class="rounded-xl bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Paiement</h2>
                    <p class="mt-2 text-sm text-slate-700">Montant: {{ number_format((float) $order->payment->amount, 0, ',', ' ') }} FCFA</p>
                    <p class="text-sm text-slate-600">Mode: {{ ucfirst($order->payment->payment_method) }}</p>
                    <p class="text-sm text-slate-600">Date: {{ $order->payment->paid_at?->format('d/m/Y a H:i') }}</p>
                </article>
            @endif
        </aside>
    </section>
@endsection
