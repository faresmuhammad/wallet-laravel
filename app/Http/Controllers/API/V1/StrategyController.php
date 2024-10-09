<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
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
}
