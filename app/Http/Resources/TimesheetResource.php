<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimesheetResource extends JsonResource
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
            'userId' => $this->user_id,
            'projectId' => $this->project_id,
            'activityId' => $this->activity_id,
            'employee' => $this->user->name,
            'project' => $this->project->name,
            'activity' => $this->activity->name,
            'activityStart' => $this->activity_start,
            'activityEnd' => $this->activity_end,
            'description' => $this->description,
        ];
    }
}
