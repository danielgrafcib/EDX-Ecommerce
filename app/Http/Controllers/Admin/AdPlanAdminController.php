<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPlan;
use Illuminate\Http\Request;

class AdPlanAdminController extends Controller
{
    public function index()
    {
        $plans = AdPlan::orderBy('price')->get();

        return view('admin.ad_plans.index', compact('plans'));
    }

    public function edit(int $id)
    {
        $plan = AdPlan::findOrFail($id);

        return view('admin.ad_plans.edit', compact('plan'));
    }

    public function update(Request $request, int $id)
    {
        $plan = AdPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_period' => ['required', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $plan->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'price' => $validated['price'],
            'billing_period' => $validated['billing_period'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.ad_plans.index')
            ->with('status', 'Plan publicitaire mis à jour.');
    }
}


