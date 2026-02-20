<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use App\Models\Product;
use App\Models\AdPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EnterpriseAdminController extends Controller
{
    public function index()
    {
        $q = request('q');
        $status = request('status');
        $enterprises = Enterprise::query()
            ->when($q, fn($qr) => $qr->where(function ($x) use ($q) {
                $x->where('name', 'like', '%'.$q.'%')
                    ->orWhere('location', 'like', '%'.$q.'%');
            }))
            ->when($status, fn($qr) => $qr->where('status', $status))
            ->with(['subscriptions.plan'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.enterprises.index', compact('enterprises'));
    }

    public function create()
    {
        return view('admin.enterprises.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'unique:enterprises,slug'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:180'],
            'website' => ['nullable', 'url'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'is_active' => ['boolean'],
        ]);

        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['is_active'] = $request->boolean('is_active');

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $name = uniqid('enterprise_', true).'.'.$file->getClientOriginalExtension();
            $stored = 'enterprises/'.$name;
            Storage::disk('public')->putFileAs('enterprises', $file, $name);
            $logoPath = '/storage/'.$stored;
        }

        Enterprise::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'website' => $validated['website'] ?? null,
            'logo_path' => $logoPath,
            'status' => $validated['status'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.enterprises.index')->with('status', 'Entreprise créée.');
    }

    public function edit(int $id)
    {
        $enterprise = Enterprise::with('products')->findOrFail($id);
        $products = Product::whereNull('enterprise_id')->orderBy('name')->paginate(20);
        $adPlans = \App\Models\AdPlan::where('is_active', true)->get();
        return view('admin.enterprises.edit', compact('enterprise', 'products', 'adPlans'));
    }

    public function update(Request $request, int $id)
    {
        $enterprise = Enterprise::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'unique:enterprises,slug,'.$enterprise->id],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:180'],
            'website' => ['nullable', 'url'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'is_active' => ['boolean'],
        ]);

        $validated['status'] = $validated['status'] ?? $enterprise->status;
        $validated['is_active'] = $request->boolean('is_active');

        $logoPath = $enterprise->logo_path;
        if ($request->hasFile('logo')) {
            if ($logoPath && str_starts_with($logoPath, '/storage/')) {
                $relative = substr($logoPath, strlen('/storage/'));
                Storage::disk('public')->delete($relative);
            }
            $file = $request->file('logo');
            $name = uniqid('enterprise_', true).'.'.$file->getClientOriginalExtension();
            $stored = 'enterprises/'.$name;
            Storage::disk('public')->putFileAs('enterprises', $file, $name);
            $logoPath = '/storage/'.$stored;
        }

        $enterprise->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'website' => $validated['website'] ?? null,
            'logo_path' => $logoPath,
            'status' => $validated['status'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.enterprises.edit', $enterprise->id)->with('status', 'Entreprise mise à jour.');
    }

    public function destroy(int $id)
    {
        $enterprise = Enterprise::findOrFail($id);
        if ($enterprise->logo_path && str_starts_with($enterprise->logo_path, '/storage/')) {
            $relative = substr($enterprise->logo_path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $enterprise->delete();

        return redirect()->route('admin.enterprises.index')->with('status', 'Entreprise supprimée.');
    }

    public function attachProduct(int $id, Request $request)
    {
        $enterprise = Enterprise::findOrFail($id);
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);
        Product::where('id', $validated['product_id'])->update(['enterprise_id' => $enterprise->id]);

        return redirect()->route('admin.enterprises.edit', $enterprise->id)->with('status', 'Produit rattaché.');
    }

    public function detachProduct(int $id, int $productId)
    {
        $enterprise = Enterprise::findOrFail($id);
        Product::where('id', $productId)->where('enterprise_id', $enterprise->id)->update(['enterprise_id' => null]);

        return redirect()->route('admin.enterprises.edit', $enterprise->id)->with('status', 'Produit détaché.');
    }

    public function subscribe(int $id, Request $request)
    {
        $enterprise = Enterprise::findOrFail($id);
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:ad_plans,id'],
            'billing_period' => ['nullable', 'in:monthly,yearly'],
        ]);
        $plan = AdPlan::findOrFail($validated['plan_id']);
        Subscription::create([
            'enterprise_id' => $enterprise->id,
            'ad_plan_id' => $plan->id,
            'start_at' => now(),
            'end_at' => null,
            'status' => 'active',
        ]);
        return redirect()->route('admin.enterprises.edit', $enterprise->id)->with('status', 'Abonnement activé.');
    }
}
