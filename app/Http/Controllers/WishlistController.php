<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function add(Request $request)
    {
        if (!$request->user()) {
            return redirect('/login');
        }
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);
        Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);
        return back()->with('status', 'Ajouté à votre wishlist.');
    }

    public function remove(Request $request, int $productId)
    {
        if (!$request->user()) {
            return redirect('/login');
        }
        Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();
        return back()->with('status', 'Retiré de votre wishlist.');
    }
}
