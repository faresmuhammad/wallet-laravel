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

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Wallet $wallet) {
            if (auth()->user())
                $wallet->user_id = auth()->id();
            else
                $wallet->user_id = 1;
        });

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function strategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(StrategyRule::class);
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
