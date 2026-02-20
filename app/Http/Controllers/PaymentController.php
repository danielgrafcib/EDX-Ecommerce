<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function initiate(Request $request, Order $order)
    {
        $request->validate([
            'provider' => 'required|string|in:tmoney,flooz,stripe',
        ]);

        $provider = $request->input('provider');
        
        try {
            $response = $this->paymentService->initiatePayment($order, $provider);
            
            if ($response->success && $response->redirectUrl) {
                return redirect($response->redirectUrl);
            }
            
            return back()->with('error', 'Payment initiation failed: ' . ($response->providerData['error'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            return back()->with('error', 'Payment Error: ' . $e->getMessage());
        }
    }

    public function handleWebhook(Request $request, string $provider)
    {
        // The VerifyWebhookSignature middleware has already run if applied.
        
        $response = $this->paymentService->handleWebhook($provider, $request);

        if (!$response->success) {
            return response()->json(['status' => 'failed', 'message' => $response->providerData['error'] ?? 'Unknown error'], 400);
        }

        // Return response expected by provider (200 OK)
        return response()->json(['status' => 'success']);
    }
}
