<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingAdminController extends Controller
{
    public function edit()
    {
        $fields = [
            'site_name' => Setting::value('site_name', config('app.name')),
            'logo_url' => Setting::value('logo_url'),
            'primary_color' => Setting::value('primary_color', '#0ea5e9'),
            'email_from' => Setting::value('email_from'),
            'payment_methods' => Setting::value('payment_methods', 'stripe,paypal,cash_on_delivery'),
            'shipping_fee' => Setting::value('shipping_fee', '0'),
            'ads_interval_ms' => Setting::value('ads_interval_ms', '3000'),
        ];
        return view('admin.settings.edit', compact('fields'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string'],
            'logo_url' => ['nullable', 'url'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'primary_color' => ['nullable', 'string'],
            'email_from' => ['nullable', 'email'],
            'payment_methods' => ['nullable', 'string'],
            'shipping_fee' => ['nullable', 'numeric'],
            'ads_interval_ms' => ['nullable', 'integer', 'min:1000'],
        ]);
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->storePublicly('settings', ['disk' => 'public']);
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            $validated['logo_url'] = $disk->url($path);
        }
        foreach ($validated as $k => $v) {
            Setting::updateOrCreate(['key' => $k], ['value' => $v]);
        }
        return redirect()->route('admin.settings.edit')->with('status', 'Paramètres mis à jour.');
    }
}
