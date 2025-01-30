<?php

namespace App\Filament\Widgets;

use App\Services\StatisticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class BalanceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $service = app(StatisticsService::class);
        $totalBalance = $service->totalBalance();
        $stats = [];

        $stats[] = Stat::make('Total Balance In EGP', 'EGP ' . $totalBalance['totalBalanceInEGP']);

        foreach ($totalBalance['balances'] as $currency => $balance) {
            if ($currency != 'EGP') {
                $stats[] = Stat::make($currency . ' Balance', Number::currency($balance['value'], $currency))
                ->description('EGP ' . $balance['EGPValue']);
            }else{
                $stats[] = Stat::make($currency . ' Balance', Number::currency($balance,$currency));
            }
        }
        return $stats;
    }
}
