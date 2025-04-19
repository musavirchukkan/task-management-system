<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskCompleteRequest extends FormRequest
{
    /**
     * The task instance
     */
    protected ?Task $task = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        try {
            $this->task = Task::findOrFail($this->route('id'));
            // Check if the authenticated user can update this task
            return $this->user()->can('update', $this->task);
        } catch (ModelNotFoundException $e) {
            // Task not found, returning false will trigger failedAuthorization
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // No need to query the task again if we already have it
            if ($this->task && $this->task->status instanceof TaskStatus) {
                if ($this->task->status !== TaskStatus::PENDING) {
                    $validator->errors()->add(
                        'status',
                        'Task is already ' . $this->task->status->value . ', Only pending tasks can be completed.'
                    );
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        // If we tried to find the task but couldn't, return a more specific error
        if (!$this->task) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Task not found',
                    'error' => 'task_not_found'
                ], 404)
            );
        }

        // Otherwise it's a regular authorization error
        throw new HttpResponseException(
            response()->json([
                'message' => 'You are not authorized to complete this task.',
                'error' => 'unauthorized_action'
            ], 403)
        );
    }
}
