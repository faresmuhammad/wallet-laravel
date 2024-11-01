<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->record->id,
            'name' => $this->record->name,
            'amount' => $this->amount,
            'senderWallet' => [
                'id' => $this->senderWallet->id,
                'name' => $this->senderWallet->name,
                'balance' => $this->senderWallet->balance,
            ],
            'receiverWallet' => [
                'id' => $this->receiverWallet->id,
                'name' => $this->receiverWallet->name,
                'balance' => $this->receiverWallet->balance,
            ],
            'date' => formatDate($this->date)
        ];
    }
}
