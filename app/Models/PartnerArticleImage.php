<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerArticleImage extends Model
{
    protected $fillable = ['partner_article_id','path','is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function article()
    {
        return $this->belongsTo(PartnerArticle::class, 'partner_article_id');
    }
}

