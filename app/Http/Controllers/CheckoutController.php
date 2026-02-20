<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Booking;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Setting;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected function resolveCart(Request $request): Cart
    {
        $sessionId = $request->session()->getId();
        $userId = optional($request->user())->id;
        $cart = Cart::query()
            ->where('status', 'active')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->with('items.product')
            ->firstOrFail();
        return $cart;
    }

    public function place(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'line1' => ['required', 'string'],
            'line2' => ['nullable', 'string'],
            'city' => ['required', 'string'],
            'state' => ['nullable', 'string'],
            'postal_code' => ['required', 'string'],
            'country' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
        ]);

        $cart = $this->resolveCart($request);

        $order = DB::transaction(function () use ($cart, $validated, $request) {
            $subtotal = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
            $discount = 0.0;
            $couponCode = null;
            $codesEnabled = (bool) (int) Setting::value('feature_codes_promo_enabled', '1');
            if ($codesEnabled && $cart->coupon_code) {
                $coupon = Coupon::where('code', $cart->coupon_code)->lockForUpdate()->first();
                if ($coupon) {
                    if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
                        abort(422, 'Code promo non disponible.');
                    }
                    $discount = $coupon->discountAmount((float)$subtotal);
                    $coupon->usage_count = (int)$coupon->usage_count + 1;
                    $coupon->save();
                    $couponCode = $coupon->code;
                }
            }
            $shipping = (float) Setting::value('shipping_fee', 0);
            $taxRate = (float) Setting::value('tax_rate', 0);
            $taxTotal = round(((float)$subtotal - (float)$discount + (float)$shipping) * $taxRate, 2);
            $total = max(0, (float)$subtotal - (float)$discount + (float)$shipping + (float)$taxTotal);
            $order = Order::create([
                'user_id' => optional($request->user())->id,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'shipping_fee' => $shipping,
                'tax_total' => $taxTotal,
                'total' => $total,
                'coupon_code' => $couponCode,
            ]);
            foreach ($cart->items as $item) {
                $orderItemData = [
                    'order_id' => $order->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];

                if ($item->service_id) {
                    $orderItemData['service_id'] = $item->service_id;
                    OrderItem::create($orderItemData);

                    Booking::create([
                        'service_id' => $item->service_id,
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'start_at' => $item->start_at,
                        'end_at' => $item->end_at,
                        'total_price' => $item->unit_price * $item->quantity,
                        'status' => 'pending',
                    ]);
                } else {
                    $orderItemData['product_id'] = $item->product_id;
                    OrderItem::create($orderItemData);
                }
            }
            $address = Address::create(array_merge($validated, ['order_id' => $order->id, 'type' => 'shipping']));

            $grouped = [];
            foreach ($cart->items as $item) {
                if ($item->service_id) {
                    continue;
                }
                $fromMarketId = optional($item->product)->market_id;
                $key = $fromMarketId ?: 0;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = 0.0;
                }
                $grouped[$key] += (float) ($item->quantity * $item->unit_price);
            }
            foreach ($grouped as $marketId => $amount) {
                Shipment::create([
                    'order_id' => $order->id,
                    'from_market_id' => $marketId ?: null,
                    'to_city' => $address->city,
                    'fee' => max(0, round($amount * 0.02, 2)),
                    'status' => 'pending',
                ]);
            }
            $cart->status = 'ordered';
            $cart->save();
            return $order->load('items.product');
        });

        return response()->json([
            'order' => $order,
            'breakdown' => [
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount_total,
                'shipping' => (float) $order->shipping_fee,
                'tax' => (float) $order->tax_total,
                'total' => (float) $order->total,
                'coupon' => $order->coupon_code,
            ],
        ], 201);
    }
}
