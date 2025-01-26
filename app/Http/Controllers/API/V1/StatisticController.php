<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatisticRequest;
use App\Http\Resources\SatisticResource;
use App\Models\Statistic;

class StatisticController extends Controller
{
    public function index()
    {
        return apiResponse('Statistics Retrieved', SatisticResource::collection(auth()->user()->statistics));
    }

    public function store(StatisticRequest $request)
    {
        $statistic = auth()->user()->statistics()->create($request->all());
        
        return apiResponse('Statistic Created', SatisticResource::make($statistic));
    }
}
