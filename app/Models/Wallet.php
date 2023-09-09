<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'color', 'initial_balance', 'include_to_stats'
    ];

    protected static function booted(): void
    {
        static::creating(function (Wallet $wallet) {
            if (auth()->user())
                $wallet->user_id = auth()->id();
            else
                $wallet->user_id = 1;
        });

        static::created(function (Wallet $wallet) {
            $wallet->balances()->create([
                'value' => $wallet->initial_balance,
                'wallet_id' => $wallet->id,
                'currency_id' => 1
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    public function budgets(): BelongsToMany
    {
        return $this->belongsToMany(Budget::class, 'budget_wallet_pivot');

    }
}
