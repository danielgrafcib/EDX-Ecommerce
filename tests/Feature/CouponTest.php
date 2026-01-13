<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_coupon_and_checkout_total_is_discounted(): void
    {
        $user = User::factory()->create();
        $product = Product::create(['name' => 'Produit test', 'slug' => 'produit-test', 'price' => 100, 'stock' => 10, 'is_active' => true]);
        $coupon = Coupon::create(['code' => 'TEST10', 'type' => 'percent', 'value' => 10, 'status' => 'active']);

        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2, 'unit_price' => 100]);

        $this->actingAs($user)
            ->post('/cart/coupon', ['code' => 'TEST10'])
            ->assertRedirect('/cart');

        $this->assertDatabaseHas('carts', ['id' => $cart->id, 'coupon_code' => 'TEST10']);

        $this->actingAs($user)
            ->get('/checkout')
            ->assertStatus(200);

        $this->actingAs($user)
            ->post('/checkout', [
                'name' => 'John Doe',
                'line1' => '1 rue du Test',
                'line2' => '',
                'city' => 'Paris',
                'state' => '',
                'postal_code' => '75000',
                'country' => 'FR',
                'phone' => '0600000000',
            ])
            ->assertCreated();

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals(180.0, (float) $order->total);
    }
}
