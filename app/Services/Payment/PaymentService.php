<?php

namespace App\Services\Payment;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Payment\Providers\FloozGateway;
use App\Services\Payment\Providers\StripeGateway;
use App\Services\Payment\Providers\TMoneyGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Resolve the gateway instance.
     */
    protected function getGateway(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            'tmoney' => app(TMoneyGateway::class),
            'flooz' => app(FloozGateway::class),
            'stripe' => app(StripeGateway::class),
            default => throw new \InvalidArgumentException("Unsupported payment provider: {$provider}"),
        };
    }

    /**
     * Initiate a payment flow.
     */
    public function initiatePayment(Order $order, string $provider): PaymentResponse
    {
        $gateway = $this->getGateway($provider);
        return $gateway->initiatePayment($order);
    }

    /**
     * Handle incoming webhooks securely and atomically.
     */
    public function handleWebhook(string $providerName, Request $request): PaymentResponse
    {
        $gateway = $this->getGateway($providerName);

        // 1. Verify Signature (Security)
        if (!$gateway->verifySignature($request)) {
            Log::warning("Invalid signature for {$providerName} webhook", ['ip' => $request->ip()]);
            return new PaymentResponse(false, null, null, ['error' => 'Invalid signature']);
        }

        // 2. Parse Payload
        $response = $gateway->handleWebhook($request);

        if (!$response->success) {
            Log::info("Payment failed or pending for {$providerName}", $response->providerData);
            return $response;
        }

        $txRef = $response->transactionReference;
        if (!$txRef) {
            return new PaymentResponse(false, null, null, ['error' => 'No transaction reference found']);
        }

        // 3. Atomic Lock (Idempotency)
        // Lock for 10 seconds to prevent double processing of the same webhook
        $lock = Cache::lock("payment_webhook_{$txRef}", 10);

        if (!$lock->get()) {
            // Couldn't get lock, meaning it's being processed right now or just finished
            Log::info("Webhook ignored due to concurrent processing: {$txRef}");
            return new PaymentResponse(true, null, $txRef, ['status' => 'ignored_concurrent']);
        }

        try {
            // 4. Check if already processed
            $existingTx = Transaction::where('reference_external', $txRef)
                ->where('status', TransactionStatus::CONFIRMED)
                ->first();

            if ($existingTx) {
                return new PaymentResponse(true, null, $txRef, ['status' => 'already_processed']);
            }

            // 5. Extract Order ID from Reference (Format: XX-ORDERID-RANDOM)
            // Example: TM-123-ABCDEF
            $parts = explode('-', $txRef);
            $orderId = isset($parts[1]) ? (int)$parts[1] : null;
            
            $order = Order::find($orderId);
            if (!$order) {
                Log::error("Order not found for ref: {$txRef}");
                return new PaymentResponse(false, null, $txRef, ['error' => 'Order not found']);
            }

            // 6. Process Business Logic (DB Transaction)
            DB::transaction(function () use ($order, $response, $providerName, $txRef) {
                // Update Order
                if ($order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing', // or confirmed
                    ]);

                    // Distribute Funds
                    $this->distributeFunds($order, $providerName, $txRef, $response->providerData);

                    // Confirm Bookings
                    foreach ($order->items as $item) {
                        if ($item->service_id) {
                            // Find the booking linked to this order and service
                            // Assuming one booking per line item for now (or finding by order_id/service_id)
                            // Better: Booking was created with order_id.
                            \App\Models\Booking::where('order_id', $order->id)
                                ->where('service_id', $item->service_id)
                                ->update(['status' => 'confirmed']);
                        }
                    }
                }
            });

            return new PaymentResponse(true, null, $txRef, ['status' => 'processed']);

        } catch (\Exception $e) {
            Log::error("Available to process webhook {$txRef}: " . $e->getMessage());
            return new PaymentResponse(false, null, $txRef, ['error' => 'Internal error']);
        } finally {
            $lock->release();
        }
    }

    /**
     * Distribute funds between vendors and platform commission.
     */
    protected function distributeFunds(Order $order, string $provider, string $externalRef, array $meta): void
    {
        // 1. Group items by Enterprise
        $enterpriseTotals = [];
        foreach ($order->items as $item) {
            // Support both Products and Services
            $sellable = $item->product ?? $item->service;

            if (!$sellable || !$sellable->enterprise_id) {
                Log::warning("Order #{$order->id} Item #{$item->id}: Sellable (Product/Service) has no enterprise. Skipping fund distribution for this item.");
                continue;
            }
            
            $entId = $sellable->enterprise_id;
            if (!isset($enterpriseTotals[$entId])) {
                $enterpriseTotals[$entId] = 0;
            }
            $enterpriseTotals[$entId] += $item->unit_price * $item->quantity;
        }

        // 2. Define Commission Rate (Could be dynamic later)
        $commissionRate = 0.05; // 5%

        // 3. Process each enterprise
        foreach ($enterpriseTotals as $entId => $amount) {
            $enterprise = \App\Models\Enterprise::find($entId);
            if (!$enterprise) continue;

            $commission = round($amount * $commissionRate, 2);
            $earnings = $amount - $commission;

            // A. Credit Vendor Enterprise
            $enterprise->ensureWalletExists()->deposit(
                $earnings, 
                TransactionType::PAYMENT, 
                [
                    'order_id' => $order->id, 
                    'provider' => $provider,
                    'ref' => $externalRef, 
                    'description' => "Vente Commande #{$order->id}"
                ]
            );

            // B. Credit System (Commission)
            // We assume an Enterprise with slug 'platform-admin' exists or we create it on the fly attached to user 1
            $systemEnterprise = \App\Models\Enterprise::firstOrCreate(
                ['slug' => 'platform-admin'],
                [
                    'name' => 'Platform Admin', 
                    'user_id' => 1, // Fallback to user 1 (Super Admin usually)
                    'description' => 'System Wallet for Commissions',
                    'is_active' => true,
                    'status' => 'verified'
                ]
            );

            if ($systemEnterprise) {
                 $systemEnterprise->ensureWalletExists()->deposit(
                    $commission,
                    TransactionType::COMMISSION,
                    [
                        'order_id' => $order->id, 
                        'source_enterprise_id' => $entId, 
                        'ref' => $externalRef,
                        'description' => "Commission 5% sur Commande #{$order->id}"
                    ]
                );
            }
        }
    }
}
