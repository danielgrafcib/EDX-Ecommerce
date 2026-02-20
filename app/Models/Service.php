<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'enterprise_id',
        'name',
        'slug',
        'category',
        'location',
        'description',
        'price',
        'rating',
        'is_available',
        'plan',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

