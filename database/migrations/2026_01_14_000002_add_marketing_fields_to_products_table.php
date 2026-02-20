<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Pricing
            $table->decimal('price_promo', 10, 2)->nullable()->after('price');
            $table->decimal('price_partner', 10, 2)->nullable()->after('price_promo');
            $table->decimal('price_premium', 10, 2)->nullable()->after('price_partner');

            // Relations
            $table->foreignId('market_id')->nullable()->after('category_id')->constrained('markets')->nullOnDelete();
            $table->foreignId('enterprise_id')->nullable()->after('market_id')->constrained('enterprises')->nullOnDelete();

            // Flags
            $table->boolean('is_service')->default(false)->after('is_active'); // Service vs Product
            $table->boolean('is_featured')->default(false)->after('is_service'); // "Mis en avant"
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['market_id']);
            $table->dropForeign(['enterprise_id']);
            $table->dropColumn([
                'price_promo', 
                'price_partner', 
                'price_premium', 
                'market_id', 
                'enterprise_id', 
                'is_service',
                'is_featured'
            ]);
        });
    }
};
