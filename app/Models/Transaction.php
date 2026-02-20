<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'fee',
        'status',
        'payment_method',
        'reference_external',
        'reference_unique',
        'description',
        'meta',
        'confirmed_at',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'meta' => 'array',
        'confirmed_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
    
    // Scope helpers
    public function scopeConfirmed($query)
    {
        return $query->where('status', TransactionStatus::CONFIRMED);
    }
}
