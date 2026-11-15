<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'priority'       => $this->priority,
            'deadline'       => $this->deadline?->format('Y-m-d'),
            'tasks_count'    => $this->whenCounted('tasks'),
            'versions_count' => $this->whenCounted('versions'),
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}