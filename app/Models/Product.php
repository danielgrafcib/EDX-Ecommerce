<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'partner_id',
        'market_id',
        'enterprise_id',
        'name',
        'slug',
        'description',
        'price',
        'price_promo',
        'price_partner',
        'price_premium',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_promo' => 'decimal:2',
        'price_partner' => 'decimal:2',
        'price_premium' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getDisplayPriceAttribute()
    {
        if ($this->price_promo !== null) {
            return $this->price_promo;
        }

        if ($this->price_partner !== null) {
            return $this->price_partner;
        }

        if ($this->price_premium !== null) {
            return $this->price_premium;
        }

        return $this->price;
    }
}
