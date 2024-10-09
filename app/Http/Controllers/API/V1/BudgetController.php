<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use App\Services\CreateNewBudget;
use App\Services\UpdateBudget;
use Illuminate\Http\Request;

//todo: these methods will modified to be suitable with modified system functionality
class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::active()->repeatable()->get();
        return BudgetResource::collection($budgets);
    }

    public function view(Budget $budget)
    {
        return new BudgetResource($budget);
    }

    public function store(CreateNewBudget $service, NewBudgetRequest $request)
    {
        return $service->create($request);
    }

    public function update(Budget $budget, UpdateBudgetRequest $request, UpdateBudget $service)
    {
        //todo: the editable budget can be master or repeatable
        return $service->update($budget, $request);
    }
}
