<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->json('phone_numbers_json')->nullable()->after('enterprise_id');
            $table->json('gallery_json')->nullable()->after('phone_numbers_json');
        });
    }

    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn(['phone_numbers_json', 'gallery_json']);
        });
    }
};

