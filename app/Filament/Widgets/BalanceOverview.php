<?php

namespace App\Filament\Widgets;

use App\Services\StatisticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class BalanceOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $service = app(StatisticsService::class);
        $totalBalance = $service->totalBalance();
        $stats = [];

        $stats[] = Stat::make('Total Balance In EGP', Number::currency($totalBalance['totalBalanceInEGP'], 'EGP'));

        foreach ($totalBalance['balances'] as $currency => $balance) {
            if ($currency != 'EGP') {
                $stats[] = Stat::make($currency . ' Balance', Number::currency($balance['value'], $currency))
                    ->description(Number::currency($balance['EGPValue'], 'EGP') . ' ' . $balance['lastUpdated']);
            } else {
                $stats[] = Stat::make($currency . ' Balance', Number::currency($balance, $currency));
            }
        }
        return $stats;
    }
}
