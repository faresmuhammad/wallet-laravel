<?php

namespace App\Services;

use App\Enums\BudgetType;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Illuminate\Http\JsonResponse;

class UpdateBudget
{


    public function update(Budget $budget, UpdateBudgetRequest $request): JsonResponse
    {

        $updatedEndTime = $budget->master_id === null ?
            $request->safe()->only('end_at')
            : ['end_at' => $budget->end_at];
        $budget->update($request->safe()->except('end_at') + $updatedEndTime);

        return new JsonResponse([
            'status' => 'Success',
            'budget' => new BudgetResource($budget)
        ]);
    }
}
