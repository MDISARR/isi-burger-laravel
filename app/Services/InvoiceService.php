<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class InvoiceService
{
    public function generate(Order $order): string
    {
        $order->loadMissing(['items', 'payment']);

        if (! in_array($order->status, [Order::STATUS_READY, Order::STATUS_PAID], true)) {
            throw new InvalidArgumentException('La facture PDF ne peut etre generee qu\'a partir du statut Prete.');
        }

        if ($order->invoice_path && Storage::disk('public')->exists($order->invoice_path)) {
            return $order->invoice_path;
        }

        $filename = 'invoices/facture-'.$order->reference.'-'.now()->format('YmdHis').'.pdf';

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
        ]);

        Storage::disk('public')->put($filename, $pdf->output());

        $order->update([
            'invoice_path' => $filename,
            'invoice_generated_at' => now(),
        ]);

        return $filename;
    }
}
