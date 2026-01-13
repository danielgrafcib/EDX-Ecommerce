<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('partner_article_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_article_id')->constrained('partner_articles')->cascadeOnDelete();
            $table->foreignId('article_category_id')->constrained('article_categories')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['partner_article_id','article_category_id']);
        });

        Schema::create('partner_article_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_article_id')->constrained('partner_articles')->cascadeOnDelete();
            $table->foreignId('article_tag_id')->constrained('article_tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['partner_article_id','article_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_article_category');
        Schema::dropIfExists('partner_article_tag');
    }
};

