<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use \App\Traits\HasWallet; // Enterprise has its own wallet for earnings
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'location',
        'website',
        'logo_path',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'permissions')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}

