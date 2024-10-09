<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Strategy extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(StrategyRule::class);
    }
}
