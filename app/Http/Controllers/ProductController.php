<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with('images', 'category', 'partner');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->integer('partner_id'));
        }

        if ($request->filled('q')) {
            $term = trim($request->get('q'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            });
        }

        if ($request->has(['price_min'])) {
            $query->where('price', '>=', $request->float('price_min', 0));
        }

        if ($request->has(['price_max'])) {
            $query->where('price', '<=', $request->float('price_max', 0));
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $sort = $request->get('sort');
        $query->when($sort === 'price_asc', fn($q) => $q->orderBy('price', 'asc'))
            ->when($sort === 'price_desc', fn($q) => $q->orderBy('price', 'desc'))
            ->when($sort === 'popular', fn($q) => $q->orderBy('stock', 'desc'))
            ->when(!in_array($sort, ['price_asc','price_desc','popular'], true), fn($q) => $q->latest());

        $perPage = min(max($request->integer('per_page', 12), 6), 60);

        return response()->json($query->paginate($perPage));
    }

    public function show(int $id)
    {
        $product = Product::with('images', 'category', 'partner')->findOrFail($id);
        return response()->json($product);
    }

    public function suggest(Request $request)
    {
        $term = trim($request->get('q', ''));
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $query = Product::query()
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', '%'.$term.'%')
                  ->orWhere('description', 'like', '%'.$term.'%');
            });
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->float('price_min', 0));
        }
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->float('price_max', 0));
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $suggestions = $query->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'price', 'slug']);

        return response()->json($suggestions);
    }
}
