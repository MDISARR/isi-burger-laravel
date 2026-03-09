<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $order->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        h1 { margin: 0; font-size: 20px; }
        .muted { color: #6b7280; }
        .header, .client, .total { margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f8fafc; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facture ISI BURGER</h1>
        <p class="muted">Reference commande: {{ $order->reference }}</p>
        <p class="muted">Date de generation: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="client">
        <strong>Informations client</strong>
        <p>Nom: {{ $order->client_name }}</p>
        <p>Telephone: {{ $order->client_phone }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantite</th>
                <th>Prix unitaire</th>
                <th>Total ligne</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->burger_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="right">{{ number_format((float) $item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format((float) $item->line_total, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p class="right"><strong>Montant total: {{ number_format((float) $order->total_amount, 0, ',', ' ') }} FCFA</strong></p>
        <p class="right">Statut commande: {{ $order->statusLabel() }}</p>
        @if ($order->payment)
            <p class="right">Paiement: {{ number_format((float) $order->payment->amount, 0, ',', ' ') }} FCFA le {{ $order->payment->paid_at?->format('d/m/Y H:i') }}</p>
        @endif
    </div>
</body>
</html>
