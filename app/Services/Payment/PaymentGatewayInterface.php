<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment request to the provider (TMoney/Flooz/Stripe).
     * Returns a redirect URL or payment instruction.
     */
    public function initiatePayment(Order $order): PaymentResponse;

    /**
     * Verify the webhook signature from the provider.
     * Use HMAC or provider specific logic.
     */
    public function verifySignature(Request $request): bool;

    /**
     * Handle the webhook payload and returns a standardized status Result.
     */
    public function handleWebhook(Request $request): PaymentResponse;
}
