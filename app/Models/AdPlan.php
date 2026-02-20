<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdPlan extends Model
{
    protected $fillable = ['name', 'slug', 'features_json', 'price', 'billing_period', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'features_json' => 'array',
    ];
}

