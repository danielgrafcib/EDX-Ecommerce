<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['name','slug','description','location','website','is_active','logo_path'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function articles()
    {
        return $this->hasMany(PartnerArticle::class);
    }

    public function images()
    {
        return $this->hasMany(PartnerImage::class);
    }
}
