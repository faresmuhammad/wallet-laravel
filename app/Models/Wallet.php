<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'color', 'initial_balance', 'include_to_stats'
    ];

    protected static function booted()
    {
        static::creating(function (Wallet $wallet) {
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
