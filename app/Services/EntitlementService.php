<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\AdEntitlement;
use App\Models\Subscription;

class EntitlementService
{
    public function resolveForAdvertisement(Advertisement $ad): array
    {
        $override = AdEntitlement::where('advertisement_id', $ad->id)->first();
        if ($override) {
            return [
                'can_view_more' => (bool)$override->can_view_more,
                'phone_quota' => (int)$override->phone_quota,
                'photo_quota' => (int)$override->photo_quota,
                'popup_variant' => $override->popup_variant,
            ];
        }

        $subscription = null;
        if ($ad->enterprise_id) {
            $subscription = Subscription::where('enterprise_id', $ad->enterprise_id)
                ->where('status', 'active')
                ->orderByDesc('start_at')
                ->first();
        }

        $features = [
            'can_view_more' => false,
            'phone_quota' => 0,
            'photo_quota' => 0,
            'popup_variant' => null,
        ];

        if ($subscription && $subscription->plan && is_array($subscription->plan->features_json)) {
            $f = $subscription->plan->features_json;
            $features['can_view_more'] = (bool)($f['can_view_more'] ?? false);
            $features['phone_quota'] = (int)($f['phone_quota'] ?? 0);
            $features['photo_quota'] = (int)($f['photo_quota'] ?? 0);
            $features['popup_variant'] = $f['popup_variant'] ?? null;
        } else {
            if (in_array($ad->payment_model, ['click', 'subscription_premium'], true)) {
                $features['can_view_more'] = true;
                $features['phone_quota'] = 1;
                $features['photo_quota'] = 3;
                $features['popup_variant'] = null;
            }
        }

        return $features;
    }
}

