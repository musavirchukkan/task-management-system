<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class TaskIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if the authenticated user can view tasks
        return $this->user()->can('viewAny', Task::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(TaskStatus::values())],
            'assigned_to' => 'nullable|exists:users,id',
            'created_by' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'search' => 'nullable|string|max:255',
            'sort_by' => ['nullable', 'string', Rule::in([
                'id', 'title', 'status', 'due_date', 'created_at', 'updated_at'
            ])],
            'sort_direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The status must be one of: ' . implode(', ', TaskStatus::values()) . '.',
            'assigned_to.exists' => 'The assigned user must exist in the users table.',
            'created_by.exists' => 'The creator user must exist in the users table.',
            'sort_by.in' => 'The sort field must be one of: id, title, status, due_date, created_at, updated_at.',
            'sort_direction.in' => 'The sort direction must be either "asc" or "desc".',
            'per_page.min' => 'The items per page must be at least 1.',
            'per_page.max' => 'The items per page cannot exceed 100.',
        ];
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
}
