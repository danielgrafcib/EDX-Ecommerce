<?php

namespace App\Services\Payment\Providers;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\PaymentResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FloozGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        // Config for Flooz
    }

    public function initiatePayment(Order $order): PaymentResponse
    {
        $ref = 'FL-' . $order->id . '-' . Str::random(6);
        
        // Mock Flooz pay url
        $paymentUrl = "https://pay.moov-africa.tg/flooz/?ref={$ref}&amount={$order->total}";

        return new PaymentResponse(
            success: true,
            redirectUrl: $paymentUrl,
            transactionReference: $ref,
            providerData: ['ref' => $ref]
        );
    }

    public function verifySignature(Request $request): bool
    {
        return true; 
    }

    public function handleWebhook(Request $request): PaymentResponse
    {
        $data = $request->all();
        // Assume Flooz sends simply 'status' => 'OK'
        if (($data['status'] ?? '') !== 'OK') {
             return new PaymentResponse(false);
        }

        return new PaymentResponse(
            success: true,
            transactionReference: $data['ref'] ?? null,
            providerData: $data
        );
    }
}
