<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'targetAmount' => $this->target_amount,
            'currentAmount' => $this->current_amount,
            'remaining' => $this->remaining,
            'period' => $this->period,
            'status' => $this->status,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'type' => $this->type,
            'master' => new BudgetResource($this->master)
        ];
    }
}
