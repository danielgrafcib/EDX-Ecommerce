<?php

namespace App\Services\Payment\Providers;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\PaymentResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TMoneyGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tmoney.base_url', 'https://tmoney.togocom.tg/api'); // Mock URL
        $this->apiKey = config('services.tmoney.api_key', 'test_key');
    }

    public function initiatePayment(Order $order): PaymentResponse
    {
        // Generation unique reference
        $ref = 'TM-' . $order->id . '-' . Str::random(6);
        
        // In a real scenario, we would make an HTTP request to TMoney here.
        // For now, we simulate the request structure.
        
        $payload = [
            'amount' => $order->total,
            'currency' => 'XOF', // TMoney uses XOF
            'external_reference' => $ref,
            'callback_url' => route('webhook.payment', ['provider' => 'tmoney']),
        ];

        // START MOCK
        // Simulating a successful request to TMoney that returns a payment URL
        $paymentUrl = "https://pay.tmoney.tg/?ref={$ref}&amount={$order->total}";
        // END MOCK

        return new PaymentResponse(
            success: true,
            redirectUrl: $paymentUrl,
            transactionReference: $ref, // TMoney's reference or ours
            providerData: $payload
        );
    }

    public function verifySignature(Request $request): bool
    {
        // TMoney usually uses a header signature or IP whitelisting
        // $signature = $request->header('X-TMoney-Signature');
        // return hash_hmac('sha256', $request->getContent(), $this->apiKey) === $signature;
        
        return true; // Mock: Accept all for now in dev
    }

    public function handleWebhook(Request $request): PaymentResponse
    {
        // Validate payload structure
        $data = $request->all();
        
        if (!isset($data['status']) || $data['status'] !== 'successful') {
            return new PaymentResponse(false, null, null, ['error' => 'Payment failed']);
        }

        return new PaymentResponse(
            success: true,
            redirectUrl: null,
            transactionReference: $data['external_reference'] ?? null, // Match with our tracking
            providerData: $data
        );
    }
}
