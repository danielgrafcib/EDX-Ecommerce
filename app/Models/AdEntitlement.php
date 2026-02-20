<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdEntitlement extends Model
{
    protected $fillable = [
        'advertisement_id',
        'can_view_more',
        'phone_quota',
        'photo_quota',
        'popup_variant',
    ];

    protected $casts = [
        'can_view_more' => 'boolean',
        'phone_quota' => 'integer',
        'photo_quota' => 'integer',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}

