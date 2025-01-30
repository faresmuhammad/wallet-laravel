<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordUpdatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'type' => $this->type,
            'category' => $this->category?->name,
            'date' => formatDate($this->date),
            'wallet' => [
                'name' => $this->wallet->name,
                'balance' => $this->wallet->balance,
            ]
        ];
    }
}
