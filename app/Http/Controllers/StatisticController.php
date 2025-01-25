<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        $statistic = Statistic::create([
            'name' => 'Test Stats',
            'type' => 'sum',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-31',
            'show_by' => 'month',
            'filter_by' => 'category',
        ]);
        ds($statistic);
        return $statistic;
    }
}
