<?php

namespace App\Services;


use App\Http\Requests\StatisticRequest;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StatisticsService
{

    public function createStatistic(StatisticRequest $request): Statistic
    {
        $statistic = auth()->user()->statistics()->create($request->except(['categories', 'labels']));
        $statistic->categories()->sync($request->categories);
        $statistic->labels()->sync($request->labels);
        return $statistic;
    }

    public function updateStatistic(StatisticRequest $request, Statistic $statistic): Statistic
    {
        $statistic->update($request->except(['categories', 'labels']));
        $statistic->categories()->sync($request->categories);
        $statistic->labels()->sync($request->labels);
        return $statistic;
    }

    public function destroyStatistic(Statistic $statistic): Statistic
    {
        $statistic->categories()->detach();
        $statistic->labels()->detach();
        $statistic->delete();
        return $statistic;
    }

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
                $currencyRate = $this->rate(from: $wallet->currency);
                $rate = $currencyRate['rate'];
                $totalBalanceInEGP += $wallet->balance * $rate;
                $balances[$wallet->currency] = [
                    'currency' => $wallet->currency,
                    'value' => $balances[$wallet->currency],
                    'rate' => $rate,
                    'lastUpdated' => $currencyRate['lastUpdated'],
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
        $output = [
            'rate' => $rate,
            'lastUpdated' => Carbon::parse($response->json('timestamp'))->diffForHumans(),
        ];
        Cache::put("currency-{$from}-{$to}", $output, 60);
        return $output;
    }
}
