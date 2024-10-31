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
        if ($this->relatedWallet) {
            $walletPart = [
                'wallet' => [
                    'name' => $this->relatedWallet->name,
                    'balance' => $this->relatedWallet->balance,
                ]
            ];
        } else {
            $wallets = [];
            foreach ($this->strategy->wallets as $wallet) {
                $wallets[] = [
                    'name' => $wallet->name,
                    'balance' => $wallet->balance,
                ];
            }
            $walletPart = [
                'wallets' => JsonResource::collection($wallets)
            ];
        }
        return [
                'id' => $this->id,
                'name' => $this->name,
                'amount' => $this->amount,
                'type' => $this->type,
                'category' => $this->category?->name,
                'date' => formatDate($this->date)
            ] + $walletPart;
    }
}
