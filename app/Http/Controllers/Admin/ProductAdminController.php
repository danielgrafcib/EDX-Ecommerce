<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Partner;
use App\Models\Market;
use App\Models\Enterprise;
use App\Models\ProductImage;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageOptimizer;

class ProductAdminController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $partners = Partner::orderBy('name')->get();
        $markets = Market::orderBy('name')->get();
        $enterprises = Enterprise::orderBy('name')->get();
        return view('admin.products.create', compact('categories','partners','markets','enterprises'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:products,slug'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'market_id' => ['nullable', 'integer', 'exists:markets,id'],
            'enterprise_id' => ['nullable', 'integer', 'exists:enterprises,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_promo' => ['nullable', 'numeric', 'min:0'],
            'price_partner' => ['nullable', 'numeric', 'min:0'],
            'price_premium' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image','mimes:jpeg,jpg,png,webp','max:20480'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $optimized = ImageOptimizer::process($file);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $optimized['large_jpg'],
                    'is_primary' => $i === 0,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('status', 'Produit créé avec succès.');
    }

    public function edit(int $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $partners = Partner::orderBy('name')->get();
        $markets = Market::orderBy('name')->get();
        $enterprises = Enterprise::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories','partners','markets','enterprises'));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:products,slug,'.$product->id],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'market_id' => ['nullable', 'integer', 'exists:markets,id'],
            'enterprise_id' => ['nullable', 'integer', 'exists:enterprises,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_promo' => ['nullable', 'numeric', 'min:0'],
            'price_partner' => ['nullable', 'numeric', 'min:0'],
            'price_premium' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image','mimes:jpeg,jpg,png,webp','max:20480'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $product->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $optimized = ImageOptimizer::process($file);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $optimized['large_jpg'],
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('status', 'Produit mis à jour.');
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);

        if (OrderItem::where('product_id', $product->id)->exists()) {
            return redirect()->route('admin.products.edit', $product->id)
                ->withErrors(['Ce produit est lié à des commandes et ne peut pas être supprimé. Désactivez-le à la place.']);
        }

        foreach ($product->images as $image) {
            if ($image->path && str_starts_with($image->path, '/storage/')) {
                $relative = substr($image->path, strlen('/storage/'));
                Storage::disk('public')->delete($relative);
            }
        }

        CartItem::where('product_id', $product->id)->delete();

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('status', 'Produit supprimé.');
    }

    public function deleteImage(int $id, int $imageId)
    {
        $product = Product::findOrFail($id);
        $image = ProductImage::where('product_id', $product->id)->findOrFail($imageId);
        if ($image->path && str_starts_with($image->path, '/storage/')) {
            $relative = substr($image->path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $image->delete();
        return redirect()->route('admin.products.edit', $product->id)
            ->with('status', 'Image supprimée.');
    }

    public function updateImage(Request $request, int $id, int $imageId)
    {
        $product = Product::findOrFail($id);
        $image = ProductImage::where('product_id', $product->id)->findOrFail($imageId);
        $validated = $request->validate([
            'image' => ['required', 'image','mimes:jpeg,jpg,png,webp','max:20480'],
        ]);
        $optimized = ImageOptimizer::process($request->file('image'));
        if ($image->path && str_starts_with($image->path, '/storage/')) {
            $relative = substr($image->path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $image->path = $optimized['large_jpg'];
        $image->save();
        return redirect()->route('admin.products.edit', $product->id)
            ->with('status', 'Image remplacée.');
    }

    public function setPrimary(int $id, int $imageId)
    {
        $product = Product::findOrFail($id);
        $image = ProductImage::where('product_id', $product->id)->findOrFail($imageId);
        ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
        $image->is_primary = true;
        $image->save();
        return redirect()->route('admin.products.edit', $product->id)
            ->with('status', 'Image principale définie.');
    }
}
