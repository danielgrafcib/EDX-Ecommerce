<?php

namespace App\Services\Payment\Providers;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\PaymentResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StripeGateway implements PaymentGatewayInterface
{
    public function initiatePayment(Order $order): PaymentResponse
    {
        // Stripe Checkout Session Mock
        $ref = 'ST-' . $order->id . '-' . Str::random(6);
        $paymentUrl = "https://checkout.stripe.com/pay/{$ref}"; // Mock

        return new PaymentResponse(
            success: true,
            redirectUrl: $paymentUrl,
            transactionReference: $ref,
            providerData: []
        );
    }

    public function verifySignature(Request $request): bool
    {
        // Stripe constructEvent logic
        return true; 
    }

    public function handleWebhook(Request $request): PaymentResponse
    {
        $data = $request->all();
        if (($data['type'] ?? '') === 'payment_intent.succeeded') {
             return new PaymentResponse(
                success: true, 
                transactionReference: $data['data']['object']['metadata']['order_ref'] ?? null,
                providerData: $data
             );
        }
        return new PaymentResponse(false);
    }
}
