<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HabitResource extends JsonResource
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
            'status' => $this->status,
            'is_completed_today' => $this->completed->isNotEmpty() ? 'yes' : 'no',
            'completed_at' => optional($this->completed->first())->created_at,
        ];
    }
}
