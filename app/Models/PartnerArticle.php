<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerArticle extends Model
{
    protected $fillable = ['partner_id','title','content','cover_path','published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function images()
    {
        return $this->hasMany(PartnerArticleImage::class, 'partner_article_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ArticleCategory::class, 'partner_article_category');
    }

    public function tags()
    {
        return $this->belongsToMany(ArticleTag::class, 'partner_article_tag');
    }
}
