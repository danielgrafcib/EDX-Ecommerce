<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'media_type',
        'media_path',
        'link_url',
        'is_active',
        'sort_order',
        'ad_type',
        'payment_model',
        'price',
        'start_date',
        'end_date',
        'views_count',
        'clicks_count',
        'enterprise_id',
        'phone_numbers_json',
        'gallery_json',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
        'phone_numbers_json' => 'array',
        'gallery_json' => 'array',
    ];
}
