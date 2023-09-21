<?php

namespace App\Services;

use App\Enums\BudgetType;
use App\Http\Requests\NewBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Illuminate\Http\JsonResponse;

class CreateNewBudget
{

    public function create(NewBudgetRequest $request): JsonResponse
    {

        $budget = Budget::create(
            $request->validated() +
            [
                'type' => BudgetType::Master->value,
                'user_id' => auth()->id()
            ]
        );

        return new JsonResponse([
            'status' => 'success',
            'budget' => new BudgetResource($budget)
        ], 201);
    }

}
