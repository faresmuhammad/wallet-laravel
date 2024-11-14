<?php

namespace App\Services;

use App\Models\Wallet;

class StatsService
{


    public function totalBalance(): float
    {
        $wallets = auth()->user()->wallets()->includedToStats()->get();
        return $wallets->sum('balance');
    }
}
