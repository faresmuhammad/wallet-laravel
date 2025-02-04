<?php

namespace App\Filament\Resources\StatisticResource\Pages;

use App\Filament\Resources\StatisticResource;
use App\Http\Requests\StatisticRequest;
use App\Services\StatisticsService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatistics extends ListRecords
{
    protected static string $resource = StatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Statistic')
                ->mutateFormDataUsing(function (array $data) {
                    $data['user_id'] = auth()->id();
                    ds($data);
                    return $data;
                })
//                ->using(function ( array $data, StatisticsService $service) {
//                    ds($data);
//                    $request = StatisticRequest::create('','',$data);
//                    $service->createStatistic($request);
//                }),
        ];
    }
}
