<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected function resolveCart(Request $request): Cart
    {
        $sessionId = $request->session()->getId();
        $userId = optional($request->user())->id;
        $cart = Cart::query()
            ->where('status', 'active')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->first();
        if (!$cart) {
            $cart = Cart::create(['user_id' => $userId, 'session_id' => $userId ? null : $sessionId, 'status' => 'active']);
        }
        return $cart;
    }

    public function show(Request $request)
    {
        $cart = $this->resolveCart($request)->load('items.product');
        $total = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        return response()->json(['cart' => $cart, 'total' => $total]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $cart = $this->resolveCart($request);
        $product = Product::findOrFail($validated['product_id']);
        $item = CartItem::query()->firstOrNew(['cart_id' => $cart->id, 'product_id' => $product->id]);
        $item->unit_price = $product->price;
        $item->quantity = ($item->exists ? $item->quantity : 0) + $validated['quantity'];
        $item->save();
        return response()->json($cart->load('items.product'));
    }

    public function update(Request $request, int $itemId)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $item = CartItem::findOrFail($itemId);
        $item->quantity = $validated['quantity'];
        $item->save();
        return response()->json($item->cart()->with('items.product')->first());
    }

    public function remove(int $itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $cart = $item->cart;
        $item->delete();
        return response()->json($cart->load('items.product'));
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string']
        ]);
        $cart = $this->resolveCart($request)->load('items.product');
        $subtotal = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $coupon = Coupon::where('code', strtoupper(trim($validated['code'])))->first();
        if (!$coupon || !$coupon->isValidForTotal($subtotal)) {
            return redirect('/cart')->with('status', 'Code promo invalide ou non applicable.');
        }
        $cart->coupon_code = $coupon->code;
        $cart->save();
        return redirect('/cart')->with('status', 'Code promo appliqué.');
    }

    public function removeCoupon(Request $request)
    {
        $cart = $this->resolveCart($request);
        $cart->coupon_code = null;
        $cart->save();
        return redirect('/cart')->with('status', 'Code promo retiré.');
    }
}
