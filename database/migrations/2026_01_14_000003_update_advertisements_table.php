<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('ad_type')->default('banner'); // company, service, shop, category_sponsor, banner
            $table->string('payment_model')->nullable(); // daily, click, monthly, subscription_premium
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            
            // Statistics
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('clicks_count')->default(0);

            // Linking
            $table->foreignId('enterprise_id')->nullable()->constrained('enterprises')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropForeign(['enterprise_id']);
            $table->dropColumn([
                'ad_type',
                'payment_model',
                'start_date',
                'end_date',
                'views_count',
                'clicks_count',
                'enterprise_id'
            ]);
        });
    }
};
