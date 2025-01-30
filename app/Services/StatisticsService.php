<?php

namespace App\Services;


use Illuminate\Support\Env;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StatisticsService
{

    public function totalBalance(): array
    {
        $wallets = auth()->user()->wallets;
        $totalBalanceInEGP = 0;
        $balances = [];
        foreach ($wallets as $wallet) {
            if (isset($balances[$wallet->currency]))
                $balances[$wallet->currency] += $wallet->balance;
            else
                $balances[$wallet->currency] = $wallet->balance;
        }
        foreach ($wallets as $wallet) {

            if ($wallet->currency != 'EGP') {
                $rate = $this->rate(from: $wallet->currency);
                $totalBalanceInEGP += $wallet->balance * $rate;
                $balances[$wallet->currency] = [
                    'currency' => $wallet->currency,
                    'value' => $balances[$wallet->currency],
                    'rate' => $rate,
                    'EGPValue' => $balances[$wallet->currency] * $rate,
                ];
            } else
                $totalBalanceInEGP += $wallet->balance;
        }
        return [
            'totalBalanceInEGP' => $totalBalanceInEGP,
            'balances' => $balances,
        ];
    }


    public function rate($from = 'USD', $to = 'EGP')
    {
        if (Cache::has("currency-{$from}-{$to}")) {
            return Cache::get("currency-{$from}-{$to}");
        }
        $response = Http::get('https://openexchangerates.org/api/latest.json?app_id=' . Env::get('OPENEXCHANGE_APP_ID') . '&base=' . $from . '&symbols=' . $to);
        $rate = round($response->json('rates')[$to], 2);
        Cache::put("currency-{$from}-{$to}", $rate, 60);
        return $rate;
    }
}
