<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatisticRequest;
use App\Http\Resources\StatisticResource;
use App\Models\Statistic;
use App\Services\StatisticsService;

class StatisticController extends Controller
{
    public function __construct(private readonly StatisticsService $service)
    {
    }

    public function index()
    {
        return apiResponse('Statistics Retrieved', StatisticResource::collection(auth()->user()->statistics));
    }

    public function store(StatisticRequest $request)
    {
        $statistic = $this->service->createStatistic($request);
        return apiResponse('Statistic Created', new StatisticResource($statistic), status: 201);
    }

    public function update(StatisticRequest $request, Statistic $statistic)
    {
        return apiResponse('Statistic Updated', new StatisticResource($this->service->updateStatistic($request, $statistic)));
    }

    public function show(Statistic $statistic)
    {
        return apiResponse('Statistic Retrieved', new StatisticResource($statistic));
    }

    public function destroy(Statistic $statistic)
    {
        $this->service->destroyStatistic($statistic);
        return apiResponse('Statistic Deleted');
    }

    public function balance()
    {
        return apiResponse('Total Balances', $this->service->totalBalance());
    }
}
