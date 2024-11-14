<?php

namespace App\Filament\Widgets;

use App\Services\StatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $service = app(StatsService::class);
        return [
            Stat::make('Total Balance',$service->totalBalance())
            ->chart([100,200,300,500]) //todo: get balance per date
        ];
    }
}
