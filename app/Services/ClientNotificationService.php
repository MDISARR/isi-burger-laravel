<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ClientNotificationService
{
    public function sendOrderConfirmation(string $clientName, string $clientPhone, string $message): void
    {
        // Simulation de notification client (SMS/app interne) pour respecter le cahier des charges.
        Log::info('Confirmation client envoyee', [
            'client_name' => $clientName,
            'client_phone' => $clientPhone,
            'message' => $message,
        ]);
    }
}
