<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'project_id'     => $this->project_id,
            'version_number' => $this->version_number,
            'description'    => $this->description,
            'release_date'   => $this->release_date?->format('Y-m-d'),
            'tasks_count'    => $this->whenCounted('tasks'),
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}