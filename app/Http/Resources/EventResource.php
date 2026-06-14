<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\Models\Event $resource
 */
class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'date' => $this->resource->date?->toIso8601String(),
            'city' => $this->resource->city,
            'status' => $this->resource->status,
            'image_url' => $this->resource->image_url,
            'capacity' => $this->resource->capacity,
            'organizer' => [
                'id' => $this->resource->organizer?->id,
                'name' => $this->resource->organizer?->name,
            ],
            'category' => [
                'id' => $this->resource->category?->id,
                'name' => $this->resource->category?->name,
            ]
        ];
    }
}