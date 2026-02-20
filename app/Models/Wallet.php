<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'currency',
        'is_frozen',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_frozen' => 'boolean',
    ];

    /**
     * The entity that owns this wallet (User or Enterprise).
     */
    public function holder(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * transactions history.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->latest();
    }

    /**
     * Check if wallet has sufficient funds.
     */
    public function hasFunds(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Credit the wallet atomically.
     */
    public function deposit(float $amount, TransactionType $type, array $meta = [], bool $confirmed = true): Transaction
    {
        return DB::transaction(function () use ($amount, $type, $meta, $confirmed) {
            // Lock row for update to prevent race conditions
            $wallet = Wallet::where('id', $this->id)->lockForUpdate()->first();

            if ($confirmed) {
                $wallet->balance += $amount;
                $wallet->save();
            }

            // Reload local instance balance to match locked state
            $this->balance = $wallet->balance;

            return $this->transactions()->create([
                'type' => $type,
                'amount' => $amount, // Positive for credit
                'status' => $confirmed ? TransactionStatus::CONFIRMED : TransactionStatus::PENDING,
                'reference_unique' => 'TX-' . strtoupper(Str::random(10)),
                'meta' => $meta,
                'confirmed_at' => $confirmed ? now() : null,
            ]);
        });
    }

    /**
     * Debit the wallet atomically.
     * @throws \Exception
     */
    public function withdraw(float $amount, TransactionType $type, array $meta = []): Transaction
    {
        return DB::transaction(function () use ($amount, $type, $meta) {
            $wallet = Wallet::where('id', $this->id)->lockForUpdate()->first();

            if ($wallet->is_frozen) {
                throw new \Exception("Wallet is frozen.");
            }

            if (!$wallet->hasFunds($amount)) {
                throw new \Exception("Insufficient funds.");
            }

            $wallet->balance -= $amount;
            $wallet->save();

            $this->balance = $wallet->balance;

            return $this->transactions()->create([
                'type' => $type,
                'amount' => -$amount, // Negative for debit
                'status' => TransactionStatus::CONFIRMED,
                'reference_unique' => 'TX-' . strtoupper(Str::random(10)),
                'meta' => $meta,
                'confirmed_at' => now(),
            ]);
        });
    }
}
