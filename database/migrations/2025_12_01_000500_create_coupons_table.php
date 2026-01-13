<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('coupons')) {
            return;
        }
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type');
            $table->decimal('value', 8, 2);
            $table->boolean('active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->decimal('min_total', 8, 2)->default(0);
            $table->integer('max_uses')->nullable();
            $table->integer('uses')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('coupons')) {
            Schema::drop('coupons');
        }
    }
};
