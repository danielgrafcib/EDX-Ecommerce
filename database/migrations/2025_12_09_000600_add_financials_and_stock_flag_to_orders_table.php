<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders','subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0)->after('payment_status');
            }
            if (!Schema::hasColumn('orders','discount_total')) {
                $table->decimal('discount_total', 10, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('orders','shipping_fee')) {
                $table->decimal('shipping_fee', 10, 2)->default(0)->after('discount_total');
            }
            if (!Schema::hasColumn('orders','tax_total')) {
                $table->decimal('tax_total', 10, 2)->default(0)->after('shipping_fee');
            }
            if (!Schema::hasColumn('orders','coupon_code')) {
                $table->string('coupon_code')->nullable()->after('tax_total');
            }
            if (!Schema::hasColumn('orders','stock_decremented')) {
                $table->boolean('stock_decremented')->default(false)->after('coupon_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders','stock_decremented')) {
                $table->dropColumn('stock_decremented');
            }
            if (Schema::hasColumn('orders','coupon_code')) {
                $table->dropColumn('coupon_code');
            }
            if (Schema::hasColumn('orders','tax_total')) {
                $table->dropColumn('tax_total');
            }
            if (Schema::hasColumn('orders','shipping_fee')) {
                $table->dropColumn('shipping_fee');
            }
            if (Schema::hasColumn('orders','discount_total')) {
                $table->dropColumn('discount_total');
            }
            if (Schema::hasColumn('orders','subtotal')) {
                $table->dropColumn('subtotal');
            }
        });
    }
};

