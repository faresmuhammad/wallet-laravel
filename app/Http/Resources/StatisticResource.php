<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticResource extends JsonResource
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
            'type' => ucfirst($this->type),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'show_by' => ucfirst($this->show_by),
            'filter_by' => ucfirst($this->filter_by),
            'categories' => CategoryResource::collection($this->categories),
            'labels' => LabelResource::collection($this->labels),
        ];
    }
}
