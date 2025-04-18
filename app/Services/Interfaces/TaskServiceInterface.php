<?php

namespace App\Services\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskServiceInterface
{
    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task;

    /**
     * Assign a task to a user.
     *
     * @param Task $task
     * @param int $userId
     * @return Task
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function assignTask(Task $task, int $userId): Task;

    /**
     * Mark a task as completed.
     *
     * @param Task $task
     * @return Task
     */
    public function completeTask(Task $task): Task;

    /**
     * Get tasks with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTasks(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Mark overdue tasks as expired.
     *
     * @return Collection
     */
    public function markOverdueTasksAsExpired(): Collection;
}
