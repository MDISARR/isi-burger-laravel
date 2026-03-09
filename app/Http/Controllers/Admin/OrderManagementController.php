<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderManagementController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {
    }

    public function index(): View
    {
        $orders = Order::query()
            ->with(['items', 'payment'])
            ->latest('placed_at')
            ->paginate(20);

        return view('admin.orders.index', [
            'orders' => $orders,
            'statusLabels' => Order::labels(),
            'statusOptions' => Order::CLIENT_ALLOWED_STATUSES,
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(['items.burger.category', 'payment']),
            'statusLabels' => Order::labels(),
            'statusOptions' => Order::CLIENT_ALLOWED_STATUSES,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        if ($order->status === Order::STATUS_CANCELED) {
            return back()->withErrors(['status' => 'Une commande annulee ne peut plus changer de statut.']);
        }

        if ($order->status === Order::STATUS_PAID) {
            return back()->withErrors(['status' => 'Une commande payee ne peut plus changer de statut ici.']);
        }

        $newStatus = $request->validated('status');
        $order->update(['status' => $newStatus]);

        if ($newStatus === Order::STATUS_READY) {
            $this->invoiceService->generate($order);
        }

        return back()->with('success', 'Statut de commande mis a jour.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        if ($order->status === Order::STATUS_PAID) {
            return back()->withErrors(['order' => 'Impossible d\'annuler une commande deja payee.']);
        }

        if ($order->status === Order::STATUS_CANCELED) {
            return back()->with('success', 'La commande est deja annulee.');
        }

        DB::transaction(function () use ($order): void {
            $order->load('items.burger');

            foreach ($order->items as $item) {
                if ($item->burger) {
                    $item->burger->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update([
                'status' => Order::STATUS_CANCELED,
                'canceled_at' => now(),
            ]);
        });

        return back()->with('success', 'Commande annulee et stock restaure.');
    }

    public function pay(StorePaymentRequest $request, Order $order): RedirectResponse
    {
        if ($order->status === Order::STATUS_CANCELED) {
            return back()->withErrors(['payment' => 'Impossible de payer une commande annulee.']);
        }

        if ($order->payment()->exists()) {
            return back()->withErrors(['payment' => 'Le paiement est deja enregistre pour cette commande.']);
        }

        $data = $request->validated();

        $order->payment()->create([
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? 'especes',
            'paid_at' => now(),
        ]);

        $order->update([
            'status' => Order::STATUS_PAID,
        ]);

        if (! $order->invoice_path) {
            $order->update(['status' => Order::STATUS_READY]);
            $this->invoiceService->generate($order);
            $order->update(['status' => Order::STATUS_PAID]);
        }

        return back()->with('success', 'Paiement enregistre avec succes.');
    }

    public function invoice(Order $order)
    {
        if (! $order->invoice_path) {
            if (! in_array($order->status, [Order::STATUS_READY, Order::STATUS_PAID], true)) {
                return back()->withErrors(['invoice' => 'La facture est disponible a partir du statut Prete.']);
            }

            $this->invoiceService->generate($order);
            $order->refresh();
        }

        if (! Storage::disk('public')->exists($order->invoice_path)) {
            return back()->withErrors(['invoice' => 'Le fichier facture est introuvable.']);
        }

        return Storage::disk('public')->download($order->invoice_path, 'facture-'.$order->reference.'.pdf');
    }
}
