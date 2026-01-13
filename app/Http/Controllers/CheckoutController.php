<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Setting;
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
            if ($cart->coupon_code) {
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
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);
            }
            Address::create(array_merge($validated, ['order_id' => $order->id, 'type' => 'shipping']));
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
