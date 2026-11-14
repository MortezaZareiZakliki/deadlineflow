<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'title'        => $this->title,
            'description'  => $this->description,
            'status'       => $this->status,
            'priority'     => $this->priority,
            'deadline'     => $this->deadline?->format('Y-m-d'),
            'project'      => 
                $this->project ? [
                    'id'   => $this->project->id,
                    'name' => $this->project->name,
                ] : null,
            'version'      => 
                $this->version ? [
                    'id'             => $this->version->id,
                    'version_number' => $this->version->version_number,
                ] : null,
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
