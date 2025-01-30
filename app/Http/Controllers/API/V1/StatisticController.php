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
        $statistic = auth()->user()->statistics()->create($request->except(['categories', 'labels']));
        $statistic->categories()->sync($request->categories);
        $statistic->labels()->sync($request->labels);
        return apiResponse('Statistic Created', new StatisticResource($statistic), status: 201);
    }

    public function update(StatisticRequest $request, Statistic $statistic)
    {
        $statistic->update($request->except(['categories', 'labels']));
        $statistic->categories()->sync($request->categories);
        $statistic->labels()->sync($request->labels);
        return apiResponse('Statistic Updated', new StatisticResource($statistic));
    }

    public function show(Statistic $statistic)
    {
        return apiResponse('Statistic Retrieved', new StatisticResource($statistic));
    }

    public function destroy(Statistic $statistic)
    {
        $statistic->categories()->detach();
        $statistic->labels()->detach();
        $statistic->delete();
        return apiResponse('Statistic Deleted');
    }

    public function balance()
    {
        return apiResponse('Total Balances', $this->service->totalBalance());
    }
}
