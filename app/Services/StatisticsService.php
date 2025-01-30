<?php

namespace App\Services;


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

            if ($wallet->currency != 'EGP') {
                $rate = $this->rate(from: $wallet->currency);
                $totalBalanceInEGP += $wallet->balance * $rate;
                $balances[$wallet->currency] = [
                    'currency' => $wallet->currency,
                    'balance' => $balances[$wallet->currency],
                    'rate' => $rate,
                    'EGPBalance' => $balances[$wallet->currency] * $rate,
                ];
            } else
                $totalBalanceInEGP += $wallet->balance;
        }
        return [
            'totalBalanceInEGP' => $totalBalanceInEGP,
            'balances' => $balances,
        ];
    }

    private function rate($from = 'USD', $to = 'EGP'): float
    {
        if (Cache::has("currency-{$from}-{$to}")) {
            return Cache::get("currency-{$from}-{$to}");
        }
        $response = Http::withHeader('apy-token', 'APY0GhvlsJRhxNesUgcfk8BnjmgvIN1X2wT40phVpIoDf3XegXngr4wj7MZiBAYHd')
            ->post("https://api.apyhub.com/data/convert/currency", [
                'source' => $from,
                'target' => $to,
            ]);
        $rate = round($response->json('data'), 2);
        Cache::put("currency-{$from}-{$to}", $rate, 60 * 24);
        return $rate;
    }
}
