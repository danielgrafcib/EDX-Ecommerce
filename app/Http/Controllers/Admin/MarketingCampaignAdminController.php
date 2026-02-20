<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaign;
use Illuminate\Http\Request;

class MarketingCampaignAdminController extends Controller
{
    public function index()
    {
        $campaigns = MarketingCampaign::query()
            ->latest()
            ->paginate(20);

        return view('admin.marketing_campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.marketing_campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:marketing_campaigns,slug'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        MarketingCampaign::create($validated);

        return redirect()->route('admin.marketing_campaigns.index')->with('status', 'Campagne créée.');
    }

    public function edit(int $id)
    {
        $campaign = MarketingCampaign::findOrFail($id);
        return view('admin.marketing_campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, int $id)
    {
        $campaign = MarketingCampaign::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:marketing_campaigns,slug,'.$campaign->id],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $campaign->update($validated);

        return redirect()->route('admin.marketing_campaigns.index')->with('status', 'Campagne mise à jour.');
    }

    public function destroy(int $id)
    {
        $campaign = MarketingCampaign::findOrFail($id);
        $campaign->delete();

        return redirect()->route('admin.marketing_campaigns.index')->with('status', 'Campagne supprimée.');
    }
}
