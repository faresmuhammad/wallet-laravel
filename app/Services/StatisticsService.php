<?php

namespace App\Services;

use Worksome\Exchange\Facades\Exchange;

class StatisticsService
{

    public function totalBalance()
    {
        $wallets = auth()->user()->wallets;
        $totalBalanceInEGP = 0;
        $balances = [];
        $rate = 50.3;//todo integrate with third party
        foreach ($wallets as $wallet) {
            if (isset($balances[$wallet->currency]))
                $balances[$wallet->currency] += $wallet->balance;
            else
                $balances[$wallet->currency] = $wallet->balance;

            if ($wallet->currency != 'EGP')
            {
                $totalBalanceInEGP += $wallet->balance * $rate;
                $balances[$wallet->currency] = [
                    'currency' => $wallet->currency,
                    'balance' => $balances[$wallet->currency],
                    'EGPBalance' => $balances[$wallet->currency] * $rate,
                ];
            }
            else
                $totalBalanceInEGP += $wallet->balance;
        }
        return [
            'totalBalanceInEGP' => $totalBalanceInEGP,
            'balances' => $balances,
        ];
    }
}
