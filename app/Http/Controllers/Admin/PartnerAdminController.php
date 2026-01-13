<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageOptimizer;

class PartnerAdminController extends Controller
{
    public function index()
    {
        $q = request('q');
        $active = request('active');
        $partners = Partner::withCount(['products','articles'])
            ->when($q, fn($qr) => $qr->where(function($x) use ($q) { $x->where('name','like','%'.$q.'%')->orWhere('location','like','%'.$q.'%'); }))
            ->when($active !== null, fn($qr) => $qr->where('is_active', (bool)$active))
            ->latest()->paginate(20);
        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string'],
            'slug' => ['required','string','unique:partners,slug'],
            'description' => ['nullable','string'],
            'location' => ['nullable','string'],
            'website' => ['nullable','url'],
            'is_active' => ['boolean'],
            'logo' => ['nullable','image','max:5120'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        if (!empty($validated['website']) && !preg_match('/^https?:\/\//i', $validated['website'])) {
            $validated['website'] = 'https://'.$validated['website'];
        }
        $partner = Partner::create($validated);
        if ($request->hasFile('logo')) {
            $optimized = ImageOptimizer::process($request->file('logo'), 'partners');
            $partner->logo_path = $optimized['thumb_jpg'];
            $partner->save();
        }
        return redirect()->route('admin.partners.index')->with('status','Partenaire créé.');
    }

    public function edit(int $id)
    {
        $partner = Partner::with(['products'=>fn($q)=>$q->with('category','images'),'images'])->findOrFail($id);
        $unlinkedProducts = Product::whereNull('partner_id')->orderBy('name')->paginate(20);
        return view('admin.partners.edit', compact('partner','unlinkedProducts'));
    }

    public function update(Request $request, int $id)
    {
        $partner = Partner::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required','string'],
            'slug' => ['required','string','unique:partners,slug,'.$partner->id],
            'description' => ['nullable','string'],
            'location' => ['nullable','string'],
            'website' => ['nullable','url'],
            'is_active' => ['boolean'],
            'logo' => ['nullable','image','max:5120'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        if (!empty($validated['website']) && !preg_match('/^https?:\/\//i', $validated['website'])) {
            $validated['website'] = 'https://'.$validated['website'];
        }
        $partner->update($validated);
        if ($request->hasFile('logo')) {
            if ($partner->logo_path && str_starts_with($partner->logo_path, '/storage/')) {
                $relative = substr($partner->logo_path, strlen('/storage/'));
                Storage::disk('public')->delete($relative);
            }
            $optimized = ImageOptimizer::process($request->file('logo'), 'partners');
            $partner->logo_path = $optimized['thumb_jpg'];
            $partner->save();
        }
        return redirect()->route('admin.partners.edit', $partner->id)->with('status','Partenaire mis à jour.');
    }

    public function destroy(int $id)
    {
        $partner = Partner::findOrFail($id);
        Product::where('partner_id', $partner->id)->update(['partner_id' => null]);
        foreach ($partner->images as $image) {
            if ($image->path && str_starts_with($image->path, '/storage/')) {
                $relative = substr($image->path, strlen('/storage/'));
                Storage::disk('public')->delete($relative);
            }
        }
        if ($partner->logo_path && str_starts_with($partner->logo_path, '/storage/')) {
            $relative = substr($partner->logo_path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $partner->delete();
        return redirect()->route('admin.partners.index')->with('status','Partenaire supprimé.');
    }

    public function attachProduct(Request $request, int $id)
    {
        $partner = Partner::findOrFail($id);
        $validated = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
        ]);
        Product::where('id',$validated['product_id'])->update(['partner_id'=>$partner->id]);
        return redirect()->route('admin.partners.edit',$partner->id)->with('status','Produit lié.');
    }

    public function detachProduct(int $id, int $productId)
    {
        $partner = Partner::findOrFail($id);
        Product::where('id',$productId)->where('partner_id',$partner->id)->update(['partner_id'=>null]);
        return redirect()->route('admin.partners.edit',$partner->id)->with('status','Produit dissocié.');
    }

    public function addImage(Request $request, int $id)
    {
        $partner = Partner::findOrFail($id);
        $validated = $request->validate([
            'image' => ['required','image','max:5120'],
        ]);
        $optimized = ImageOptimizer::process($request->file('image'), 'partners/gallery');
        \App\Models\PartnerImage::create([
            'partner_id' => $partner->id,
            'path' => $optimized['large_jpg'],
            'is_primary' => false,
        ]);
        return redirect()->route('admin.partners.edit',$partner->id)->with('status','Image ajoutée.');
    }

    public function deleteImage(int $id, int $imageId)
    {
        $partner = Partner::findOrFail($id);
        $image = \App\Models\PartnerImage::where('partner_id',$partner->id)->findOrFail($imageId);
        if ($image->path && str_starts_with($image->path, '/storage/')) {
            $relative = substr($image->path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $image->delete();
        return redirect()->route('admin.partners.edit',$partner->id)->with('status','Image supprimée.');
    }

    public function setPrimaryImage(int $id, int $imageId)
    {
        $partner = Partner::findOrFail($id);
        $image = \App\Models\PartnerImage::where('partner_id',$partner->id)->findOrFail($imageId);
        \App\Models\PartnerImage::where('partner_id',$partner->id)->update(['is_primary'=>false]);
        $image->is_primary = true;
        $image->save();
        return redirect()->route('admin.partners.edit',$partner->id)->with('status','Image principale définie.');
    }
}
