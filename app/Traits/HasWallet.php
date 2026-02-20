<?php

namespace App\Traits;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasWallet
{
    /**
     * Get the entity's wallet.
     */
    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'holder');
    }

    /**
     * Get the balance of the wallet or 0.
     */
    public function getBalanceAttribute(): float
    {
        return (float) ($this->wallet?->balance ?? 0);
    }

    /**
     * Ensure a wallet exists for this entity.
     */
    public function ensureWalletExists(): Wallet
    {
        if (!$this->wallet) {
            return $this->wallet()->create([
                'balance' => 0,
                'currency' => 'XOF'
            ]);
        }
        return $this->wallet;
    }
}
