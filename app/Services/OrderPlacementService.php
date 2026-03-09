<?php

namespace App\Services;

use App\Models\Burger;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderPlacementService
{
    public function __construct(
        private readonly ClientNotificationService $notificationService,
    ) {
    }

    /**
     * @param array{client_name:string,client_phone:string,items:array<int|string,int|string>} $payload
     */
    public function place(array $payload): Order
    {
        $quantities = collect($payload['items'] ?? [])
            ->mapWithKeys(fn ($quantity, $burgerId) => [(int) $burgerId => (int) $quantity])
            ->filter(fn (int $quantity): bool => $quantity > 0);

        if ($quantities->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Selectionnez au moins un burger avec une quantite superieure a zero.',
            ]);
        }

        return DB::transaction(function () use ($payload, $quantities): Order {
            $burgers = Burger::query()
                ->whereIn('id', $quantities->keys()->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($burgers->count() !== $quantities->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Un ou plusieurs burgers selectionnes sont introuvables.',
                ]);
            }

            $total = 0;

            foreach ($quantities as $burgerId => $quantity) {
                $burger = $burgers->get($burgerId);

                if (! $burger || $burger->is_archived) {
                    throw ValidationException::withMessages([
                        'items' => 'Un burger selectionne n\'est plus disponible.',
                    ]);
                }

                if ($burger->stock_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuffisant pour {$burger->name}.",
                    ]);
                }

                $total += $burger->price * $quantity;
            }

            $order = Order::query()->create([
                'reference' => $this->generateReference(),
                'client_name' => $payload['client_name'],
                'client_phone' => $payload['client_phone'],
                'status' => Order::STATUS_PENDING,
                'total_amount' => $total,
                'placed_at' => now(),
            ]);

            foreach ($quantities as $burgerId => $quantity) {
                $burger = $burgers->get($burgerId);
                $lineTotal = $burger->price * $quantity;

                $order->items()->create([
                    'burger_id' => $burger->id,
                    'burger_name' => $burger->name,
                    'unit_price' => $burger->price,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ]);

                $burger->decrement('stock_quantity', $quantity);
            }

            $confirmationMessage = "Merci {$order->client_name}, votre commande {$order->reference} est bien enregistree.";

            $order->update([
                'confirmation_message' => $confirmationMessage,
            ]);

            $this->notificationService->sendOrderConfirmation(
                $order->client_name,
                $order->client_phone,
                $confirmationMessage,
            );

            return $order->load(['items.burger', 'payment']);
        });
    }

    private function generateReference(): string
    {
        do {
            $reference = 'CMD-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
        } while (Order::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
