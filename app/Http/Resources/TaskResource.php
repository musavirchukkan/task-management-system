<?php

namespace App\Http\Resources;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date?->toIso8601String(),
            'assigned_to' => $this->assigned_to,
            'assigned_user' => $this->whenLoaded('assignedUser', function () {
                return new UserResource($this->assignedUser);
            }),
            'created_by' => $this->created_by,
            'created_user' => $this->whenLoaded('createdUser', function () {
                return new UserResource($this->createdUser);
            }),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            // Adding HATEOAS links
            '_links' => [
                'self' => [
                    'href' => route('tasks.tasks.show', $this->id),
                ],
                'assign' => [
                    'href' => route('tasks.assign', $this->id),
                ],
                'complete' => $this->when($this->status === TaskStatus::PENDING->value, [
                    'href' => route('tasks.complete', $this->id),
                ]),
            ],
        ];
    }
}
