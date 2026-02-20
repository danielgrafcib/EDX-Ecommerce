<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketAdminController extends Controller
{
    public function index()
    {
        $q = request('q');
        $active = request('active');
        $activeFilter = null;
        if ($active === '1') {
            $activeFilter = true;
        } elseif ($active === '0') {
            $activeFilter = false;
        }
        $markets = Market::query()
            ->when($q, fn($qr) => $qr->where('name', 'like', '%'.$q.'%')->orWhere('location', 'like', '%'.$q.'%'))
            ->when($activeFilter !== null, fn($qr) => $qr->where('is_active', $activeFilter))
            ->orderBy('name')
            ->paginate(20);

        return view('admin.markets.index', compact('markets'));
    }

    public function create()
    {
        return view('admin.markets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'unique:markets,slug'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:180'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = uniqid('market_', true).'.'.$file->getClientOriginalExtension();
            $stored = 'markets/'.$name;
            Storage::disk('public')->putFileAs('markets', $file, $name);
            $imagePath = '/storage/'.$stored;
        }

        Market::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'image_path' => $imagePath,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.markets.index')->with('status', 'Marché créé.');
    }

    public function edit(int $id)
    {
        $market = Market::findOrFail($id);
        return view('admin.markets.edit', compact('market'));
    }

    public function update(Request $request, int $id)
    {
        $market = Market::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'unique:markets,slug,'.$market->id],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:180'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $imagePath = $market->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath && str_starts_with($imagePath, '/storage/')) {
                $relative = substr($imagePath, strlen('/storage/'));
                Storage::disk('public')->delete($relative);
            }
            $file = $request->file('image');
            $name = uniqid('market_', true).'.'.$file->getClientOriginalExtension();
            $stored = 'markets/'.$name;
            Storage::disk('public')->putFileAs('markets', $file, $name);
            $imagePath = '/storage/'.$stored;
        }

        $market->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'image_path' => $imagePath,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.markets.edit', $market->id)->with('status', 'Marché mis à jour.');
    }

    public function destroy(int $id)
    {
        $market = Market::findOrFail($id);
        if ($market->image_path && str_starts_with($market->image_path, '/storage/')) {
            $relative = substr($market->image_path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $market->delete();

        return redirect()->route('admin.markets.index')->with('status', 'Marché supprimé.');
    }
}
