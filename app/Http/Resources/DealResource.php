<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'title' => $this->title,
            'value' => $this->value,
            'stage_id' => $this->stage_id,
            'contact_id' => $this->contact_id,
            'owner_id' => $this->owner_id,
            'stage' => new PipelineStageResource($this->whenLoaded('stage')),
            'contact' => new ContactResource($this->whenLoaded('contact')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
