<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Services\EntitlementService;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function viewMore(int $id, Request $request, EntitlementService $entitlementService)
    {
        $ad = Advertisement::findOrFail($id);

        $ent = $entitlementService->resolveForAdvertisement($ad);
        if (!$ent['can_view_more']) {
            return response()->json([
                'eligible' => false,
                'upgrade' => true,
                'message' => 'Mettre à niveau pour voir plus',
            ], 403);
        }

        if ($ad->payment_model === 'click') {
            $ad->increment('clicks_count');
        }

        $phones = [];
        $photos = [];

        if (is_array($ad->phone_numbers_json)) {
            $phones = array_slice($ad->phone_numbers_json, 0, max(0, (int)$ent['phone_quota']));
        }
        if (is_array($ad->gallery_json)) {
            $photos = array_slice($ad->gallery_json, 0, max(0, (int)$ent['photo_quota']));
        }

        return response()->json([
            'eligible' => true,
            'phones' => $phones,
            'photos' => $photos,
            'popup_variant' => $ent['popup_variant'],
        ]);
    }
}

