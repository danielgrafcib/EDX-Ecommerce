<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Enterprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageOptimizer;

class AdvertisementAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $active = $request->input('active');
        $type = $request->get('ad_type');
        $payment = $request->get('payment_model');
        $activeFilter = null;
        if ($active === '1') {
            $activeFilter = true;
        } elseif ($active === '0') {
            $activeFilter = false;
        }
        $ads = Advertisement::query()
            ->when($q, fn($qr) => $qr->where('title','like','%'.$q.'%'))
            ->when($activeFilter !== null, fn($qr) => $qr->where('is_active', $activeFilter))
            ->when($type, fn($qr) => $qr->where('ad_type', $type))
            ->when($payment, fn($qr) => $qr->where('payment_model', $payment))
            ->orderBy('sort_order')
            ->latest()
            ->paginate(20);
        return view('admin.ads.index', compact('ads','type','payment'));
    }

    public function create()
    {
        $enterprises = Enterprise::orderBy('name')->get();
        return view('admin.ads.create', compact('enterprises'));
    }

    public function store(Request $request)
    {
        $type = $request->input('media_type');
        $rules = [
            'title' => ['required','string','max:180'],
            'description' => ['nullable','string'],
            'media_type' => ['required','in:image,video'],
            'link_url' => ['nullable','url'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable','integer','min:0'],
            'ad_type' => ['required','in:banner,company,service,shop,category_sponsor'],
            'payment_model' => ['nullable','in:daily,click,monthly,subscription_premium'],
            'price' => ['nullable','numeric','min:0'],
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'enterprise_id' => ['nullable','integer','exists:enterprises,id'],
            'phone_numbers' => ['nullable','array'],
            'phone_numbers.*' => ['nullable','string'],
            'gallery' => ['nullable','array'],
            'gallery.*' => ['nullable','string'],
        ];
        if ($type === 'image') {
            $rules['media'] = ['required','image','mimes:jpeg,jpg,png,webp','max:20480'];
        } else {
            $rules['media'] = ['required','file','mimes:mp4,webm,mov,m4v','max:102400'];
        }
        $validated = $request->validate($rules);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $path = null;
        if ($validated['media_type'] === 'image') {
            $optimized = ImageOptimizer::process($request->file('media'), 'ads/images');
            $path = $optimized['large_jpg'];
        } else {
            $file = $request->file('media');
            $ext = strtolower($file->getClientOriginalExtension());
            $name = uniqid('ad_', true).'.'.$ext;
            $stored = 'ads/videos/'.$name;
            Storage::disk('public')->putFileAs('ads/videos', $file, $name);
            $path = '/storage/'.$stored;
        }

        $ad = Advertisement::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'media_type' => $validated['media_type'],
            'media_path' => $path,
            'link_url' => $validated['link_url'] ?? null,
            'is_active' => $validated['is_active'],
            'sort_order' => $validated['sort_order'],
            'ad_type' => $validated['ad_type'],
            'payment_model' => $validated['payment_model'] ?? null,
            'price' => isset($validated['price']) ? $validated['price'] : null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'enterprise_id' => $validated['enterprise_id'] ?? null,
            'phone_numbers_json' => $validated['phone_numbers'] ?? null,
            'gallery_json' => $validated['gallery'] ?? null,
        ]);

        return redirect()->route('admin.ads.index')->with('status','Publicité créée.');
    }

    public function edit(int $id)
    {
        $ad = Advertisement::findOrFail($id);
        $enterprises = Enterprise::orderBy('name')->get();
        return view('admin.ads.edit', compact('ad','enterprises'));
    }

    public function update(Request $request, int $id)
    {
        if ($request->hasFile('media') && !$request->file('media')->isValid()) {
            $error = $request->file('media')->getErrorMessage();
            return back()->withErrors(['media' => "Erreur upload: $error (Code: " . $request->file('media')->getError() . ") - Vérifiez upload_max_filesize dans php.ini"]);
        }

        $ad = Advertisement::findOrFail($id);
        $type = $request->input('media_type');
        $rules = [
            'title' => ['required','string','max:180'],
            'description' => ['nullable','string'],
            'media_type' => ['required','in:image,video'],
            'link_url' => ['nullable','url'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable','integer','min:0'],
            'ad_type' => ['required','in:banner,company,service,shop,category_sponsor'],
            'payment_model' => ['nullable','in:daily,click,monthly,subscription_premium'],
            'price' => ['nullable','numeric','min:0'],
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'enterprise_id' => ['nullable','integer','exists:enterprises,id'],
            'phone_numbers' => ['nullable','array'],
            'phone_numbers.*' => ['nullable','string'],
            'gallery' => ['nullable','array'],
            'gallery.*' => ['nullable','string'],
        ];
        if ($request->hasFile('media')) {
            if ($type === 'image') {
                $rules['media'] = ['nullable','image','mimes:jpeg,jpg,png,webp','max:20480'];
            } else {
                $rules['media'] = ['nullable','file','mimes:mp4,webm,mov,m4v','max:102400'];
            }
        } else {
            $rules['media'] = ['nullable'];
        }
        $validated = $request->validate($rules);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? $ad->sort_order;

        $path = $ad->media_path;
        if ($request->hasFile('media')) {
            if ($path && str_starts_with($path, '/storage/')) {
                $relative = substr($path, strlen('/storage/'));
                Storage::disk('public')->delete($relative);
            }
            if ($validated['media_type'] === 'image') {
                $optimized = ImageOptimizer::process($request->file('media'), 'ads/images');
                $path = $optimized['large_jpg'];
            } else {
                $file = $request->file('media');
                $ext = strtolower($file->getClientOriginalExtension());
                $name = uniqid('ad_', true).'.'.$ext;
                $stored = 'ads/videos/'.$name;
                Storage::disk('public')->putFileAs('ads/videos', $file, $name);
                $path = '/storage/'.$stored;
            }
        }

        $ad->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'media_type' => $validated['media_type'],
            'media_path' => $path,
            'link_url' => $validated['link_url'] ?? null,
            'is_active' => $validated['is_active'],
            'sort_order' => $validated['sort_order'],
            'ad_type' => $validated['ad_type'],
            'payment_model' => $validated['payment_model'] ?? null,
            'price' => isset($validated['price']) ? $validated['price'] : $ad->price,
            'start_date' => $validated['start_date'] ?? $ad->start_date,
            'end_date' => $validated['end_date'] ?? $ad->end_date,
            'enterprise_id' => $validated['enterprise_id'] ?? $ad->enterprise_id,
            'phone_numbers_json' => $validated['phone_numbers'] ?? $ad->phone_numbers_json,
            'gallery_json' => $validated['gallery'] ?? $ad->gallery_json,
        ]);

        return redirect()->route('admin.ads.edit', $ad->id)->with('status','Publicité mise à jour.');
    }

    public function destroy(int $id)
    {
        $ad = Advertisement::findOrFail($id);
        if ($ad->media_path && str_starts_with($ad->media_path, '/storage/')) {
            $relative = substr($ad->media_path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $ad->delete();
        return redirect()->route('admin.ads.index')->with('status','Publicité supprimée.');
    }

    public function toggle(int $id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->is_active = ! (bool)$ad->is_active;
        $ad->save();
        return redirect()->route('admin.ads.index')->with('status','Statut mis à jour.');
    }

    public function updateMedia(Request $request, int $id)
    {
        $ad = Advertisement::findOrFail($id);
        $type = $ad->media_type === 'video' ? 'video' : 'image';
        $rules = [];
        if ($type === 'image') {
            $rules['media'] = ['required','image','mimes:jpeg,jpg,png,webp','max:20480'];
        } else {
            $rules['media'] = ['required','file','mimes:mp4,webm,mov,m4v','max:102400'];
        }
        $validated = $request->validate($rules);

        $path = $ad->media_path;
        if ($request->hasFile('media')) {
            if ($path && str_starts_with($path, '/storage/')) {
                $relative = substr($path, strlen('/storage/'));
                Storage::disk('public')->delete($relative);
            }
            if ($type === 'image') {
                $optimized = ImageOptimizer::process($request->file('media'), 'ads/images');
                $path = $optimized['large_jpg'];
            } else {
                $file = $request->file('media');
                $ext = strtolower($file->getClientOriginalExtension());
                $name = uniqid('ad_', true).'.'.$ext;
                $stored = 'ads/videos/'.$name;
                Storage::disk('public')->putFileAs('ads/videos', $file, $name);
                $path = '/storage/'.$stored;
            }
        }

        $ad->media_path = $path;
        $ad->save();

        return redirect()->route('admin.ads.edit', $ad->id)->with('status','Média remplacé.');
    }
}
