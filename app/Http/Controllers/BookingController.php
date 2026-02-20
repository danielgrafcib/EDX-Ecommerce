<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Show the service booking page.
     */
    public function show(int $id)
    {
        $service = Service::with(['enterprise'])->findOrFail($id);
        
        // Check for active bookings to disable dates? 
        // Simple implementation: Just show the page.
        
        return view('booking.show', compact('service'));
    }

    /**
     * Get availability for a service.
     */
    public function availability(Request $request, int $id)
    {
        $service = Service::findOrFail($id);
        
        $start = $request->query('start');
        $end = $request->query('end');

        if (!$start || !$end) {
            return response()->json(['error' => 'Start and End dates required'], 400);
        }

        $bookings = $service->bookings()
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                  ->orWhereBetween('end_at', [$start, $end]);
            })
            ->whereIn('status', ['confirmed', 'pending'])
            ->get(['start_at', 'end_at']);

        $events = $bookings->map(function($booking) {
            return [
                'title' => 'Occupé',
                'start' => $booking->start_at->toIso8601String(),
                'end' => $booking->end_at->toIso8601String(),
                'display' => 'background',
                'color' => '#ff9f89'
            ];
        });

        return response()->json($events);
    }

    /**
     * Add booking to cart.
     */
    public function book(Request $request, int $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after:now'],
            'start_time' => ['required', 'date_format:H:i'],
            // Assuming simple 1-hour slots or fixed duration for MVP
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:8'], 
        ]);

        $duration = $validated['duration_hours'] ?? 1;
        $startAt = \Carbon\Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        $endAt = $startAt->copy()->addHours($duration);

        // Check availability (Basic overlap check)
        $exists = $service->bookings()
            ->where(function ($query) use ($startAt, $endAt) {
                $query->whereBetween('start_at', [$startAt, $endAt])
                      ->orWhereBetween('end_at', [$startAt, $endAt])
                      ->orWhere(function ($q) use ($startAt, $endAt) {
                          $q->where('start_at', '<', $startAt)
                            ->where('end_at', '>', $endAt);
                      });
            })
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['start_date' => 'Ce créneau est déjà réservé.']);
        }

        // Add to Cart
        $sessionId = Session::getId();
        $user = Auth::user();

        $cart = Cart::firstOrCreate(
            [
                'session_id' => $user ? null : $sessionId,
                'user_id' => $user ? $user->id : null,
                'status' => 'active'
            ]
        );

        // Add Item
        $cart->items()->create([
            'service_id' => $service->id,
            'quantity' => 1,
            'unit_price' => $service->price * $duration, // Price per hour assumption? Or fixed price?
            // If price is fixed per service, unit_price = $service->price.
            // If hourly, multiply. Let's assume fixed price for now based on Service model 'price'.
            // Actually, usually services are hourly. Let's stick to service price for MVP.
            // 'unit_price' => $service->price, 
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        return redirect('/cart')->with('status', 'Service ajouté au panier.');
    }
}
