@extends('layouts.app', ['title' => 'Commandes'])

@section('content')
    <section class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Suivi des commandes</h1>
        <p class="mt-1 text-sm text-slate-600">Consultez les commandes, changez le statut, annulez ou enregistrez le paiement.</p>

        <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-slate-700">Reference</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Client</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Statut</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Montant</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-3 font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white align-top">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $order->reference }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $order->client_name }}</p>
                                <p class="text-xs text-slate-500">{{ $order->client_phone }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $order->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ number_format((float) $order->total_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $order->placed_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="space-y-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Details</a>

                                    @if (!in_array($order->status, ['paid', 'canceled'], true))
                                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="rounded-md border border-slate-300 px-2 py-1 text-xs">
                                                @foreach ($statusOptions as $status)
                                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ $statusLabels[$status] }}</option>
                                                @endforeach
                                            </select>
                                            <button class="rounded-md bg-slate-900 px-3 py-1 text-xs font-semibold text-white">Maj statut</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" onsubmit="return confirm('Confirmer l\'annulation ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-md border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">Annuler</button>
                                        </form>
                                    @endif

                                    @if (!$order->payment && $order->status !== 'canceled')
                                        <form method="POST" action="{{ route('admin.orders.pay', $order) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="payment_method" value="especes">
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0.01"
                                                name="amount"
                                                value="{{ (float) $order->total_amount }}"
                                                class="w-28 rounded-md border border-slate-300 px-2 py-1 text-xs"
                                            >
                                            <button class="rounded-md bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-500">Payer</button>
                                        </form>
                                    @else
                                        <p class="text-xs text-emerald-700">Paiement enregistre</p>
                                    @endif

                                    @if ($order->invoice_path)
                                        <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex rounded-md border border-indigo-300 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-50">Facture PDF</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">Aucune commande pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $orders->links() }}</div>
    </section>
@endsection
