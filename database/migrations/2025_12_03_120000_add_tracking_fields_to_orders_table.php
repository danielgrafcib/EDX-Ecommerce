<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_carrier')->nullable()->after('payment_status');
            $table->string('tracking_code')->nullable()->after('tracking_carrier');
            $table->string('tracking_url')->nullable()->after('tracking_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_carrier','tracking_code','tracking_url']);
        });
    }
};

