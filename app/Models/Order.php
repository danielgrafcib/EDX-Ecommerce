<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'payment_status', 'subtotal', 'discount_total', 'shipping_fee', 'tax_total', 'total', 'coupon_code', 'tracking_carrier', 'tracking_code', 'tracking_url', 'stock_decremented'];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'stock_decremented' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
