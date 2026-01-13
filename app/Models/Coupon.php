<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'status', 'usage_limit', 'usage_count', 'starts_at', 'ends_at'];

    protected $casts = [
        'value' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function isValidForTotal(float $subtotal): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->starts_at && now()->lessThan($this->starts_at)) return false;
        if ($this->ends_at && now()->greaterThan($this->ends_at)) return false;
        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) return false;
        return true;
    }

    public function discountAmount(float $subtotal): float
    {
        if (!$this->isValidForTotal($subtotal)) return 0.0;
        if ($this->type === 'percent') {
            return round($subtotal * ((float)$this->value) / 100, 2);
        }
        return min($subtotal, (float)$this->value);
    }
}
