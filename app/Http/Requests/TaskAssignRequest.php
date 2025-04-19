<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskAssignRequest extends FormRequest
{
    /**
     * The task instance
     */
    protected ?Task $task = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
        return [
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.integer' => 'The user ID must be an integer.',
            'user_id.exists' => 'The selected user does not exist.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
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
                'message' => 'You are not authorized to assign this task.',
                'error' => 'unauthorized_action'
            ], 403)
        );
    }
}
