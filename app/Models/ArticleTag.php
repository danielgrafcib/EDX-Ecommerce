<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleTag extends Model
{
    protected $fillable = ['name','slug'];

    public function articles()
    {
        return $this->belongsToMany(PartnerArticle::class, 'partner_article_tag');
    }
}

