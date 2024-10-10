<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Strategy;
use App\Services\StrategyService;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    /*
     * create strategy
     * update strategy
     * activate strategy
     * migrate to a strategy
     * set rule to a strategy
     * update rule
     * remove rule from strategy
     */
    public function __construct(private StrategyService $service)
    {
    }

    public function store(Request $request)
    {
        return $this->service->createStrategy($request);
    }

    public function updateName(Request $request, Strategy $strategy)
    {
        return $this->service->updateStrategyName($request->name, $strategy);
    }

    public function activateStrategy(Strategy $strategy)
    {
        $this->service->activateStrategy($strategy);
    }
}
