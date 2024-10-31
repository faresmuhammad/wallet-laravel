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
            'name' => $this->name,
            'amount' => $this->amount,
            'type' => $this->type,
            'wallet' => [
                'name' => $this->relatedWallet->name,
                'balance' => $this->relatedWallet->balance,
            ],
            'category' => $this->category?->name,
            'date' => $this->date
        ];
    }
}
