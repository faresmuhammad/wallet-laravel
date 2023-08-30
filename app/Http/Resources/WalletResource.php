<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            'color' => $this->color,
            'initialBalance' => $this->initial_balance,
            'includedToStatistics' => $this->included_to_stats,
            'balances' => $this->balances,
            //todo: delete this, it is just for development
            'user' => $this->user
        ];
    }
}
