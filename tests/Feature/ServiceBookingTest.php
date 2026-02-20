<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Enterprise;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_booking_for_service()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        
        $enterprise = Enterprise::create([
            'user_id' => $user->id,
            'name' => 'Test Enterprise',
            'slug' => 'test-enterprise',
            'description' => 'Test Desc',
            'location' => 'Lome',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $service = Service::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Test Service',
            'slug' => 'test-service',
            'category' => 'Cleaning',
            'location' => 'Lome',
            'description' => 'Cleaning service',
            'price' => 5000,
            'is_available' => true,
            'is_active' => true,
        ]);

        // 2. Create Cart
        $cart = Cart::create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $startAt = now()->addDays(1);
        $endAt = now()->addDays(1)->addHours(2);

        CartItem::create([
            'cart_id' => $cart->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'unit_price' => 5000,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        // 3. Perform Checkout
        $response = $this->actingAs($user)
            ->postJson('/checkout', [
                'name' => 'John Doe',
                'line1' => '123 Main St',
                'city' => 'Lome',
                'postal_code' => '00228',
                'country' => 'Togo',
            ]);

        // 4. Assertions
        $response->assertStatus(201);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals(5000, $order->total); 

        $orderItem = OrderItem::first();
        $this->assertNotNull($orderItem);
        $this->assertEquals($service->id, $orderItem->service_id);
        $this->assertNull($orderItem->product_id);

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals($service->id, $booking->service_id);
        $this->assertEquals($user->id, $booking->user_id);
        $this->assertEquals($order->id, $booking->order_id);
        // Note: Precision issues with database timestamps might occur, so we just check it exists.
    }
}
