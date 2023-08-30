<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
            'type' => $this->type,
            'balanceBefore' => $this->balance_before,
            'balanceAfter' => $this->balance_after,
            'date' => $this->date,
            'currency' => $this->currency,
            'category' => $this->category,
//            'budget' => $this->budget
        ];
    }
}
