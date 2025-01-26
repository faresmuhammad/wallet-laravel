<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SatisticResource extends JsonResource
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
            'type' => ucfirst($this->type),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'show_by' => $this->show_by,
            'filter_by' => $this->filter_by,
            'categories' => $this->categories,
            'labels' => $this->labels,
        ];
    }
}
