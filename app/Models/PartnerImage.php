<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerImage extends Model
{
    protected $fillable = ['partner_id','path','is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}

