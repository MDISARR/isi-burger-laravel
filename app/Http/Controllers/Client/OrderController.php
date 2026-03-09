<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderPlacementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderPlacementService $orderPlacementService,
    ) {
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->orderPlacementService->place($request->validated());
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors(['order' => 'Impossible de finaliser la commande pour le moment.']);
        }

        return redirect()
            ->route('orders.confirmation', $order)
            ->with('success', 'Commande enregistree avec succes.');
    }

    public function confirmation(Order $order): View
    {
        return view('client.orders.confirmation', [
            'order' => $order->load('items.burger'),
        ]);
    }
}
