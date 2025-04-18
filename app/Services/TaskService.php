<?php

namespace App\Services;

use App\Events\TaskCompleted;
use App\Jobs\SendTaskAssignmentNotification;
use App\Models\Task;
use App\Models\User;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService implements TaskServiceInterface
{
    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = Task::STATUS_PENDING;
        }

        return DB::transaction(function () use ($data) {
            return Task::create($data);
        });
    }

    /**
     * Assign a task to a user.
     *
     * @param Task $task
     * @param int $userId
     * @return Task
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function assignTask(Task $task, int $userId): Task
    {
        // Check if user exists
        $user = User::findOrFail($userId);

        return DB::transaction(function () use ($task, $userId, $user) {
            $task->assigned_to = $userId;
            $task->save();

            // Dispatch job to send notification
            SendTaskAssignmentNotification::dispatch($task, $user);

            return $task;
        });
    }

    /**
     * Mark a task as completed.
     *
     * @param Task $task
     * @return Task
     */
    public function completeTask(Task $task): Task
    {
        if ($task->status !== Task::STATUS_PENDING) {
            throw new \InvalidArgumentException('Only pending tasks can be completed.');
        }

        return DB::transaction(function () use ($task) {
            $task->status = Task::STATUS_COMPLETED;
            $task->save();

            // Trigger task completed event
            event(new TaskCompleted($task));

            return $task;
        });
    }

    /**
     * Get tasks with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTasks(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Task::query();

        // Apply filters
        if (isset($filters['status']) && in_array($filters['status'], [
            Task::STATUS_PENDING,
            Task::STATUS_COMPLETED,
            Task::STATUS_EXPIRED
        ])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        $allowedSortFields = ['id', 'title', 'status', 'due_date', 'created_at', 'updated_at'];
        $sortField = in_array($filters['sort_by'] ?? '', $allowedSortFields)
            ? $filters['sort_by']
            : 'created_at';

        $sortDirection = strtolower($filters['sort_direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->with('assignedUser')->paginate($perPage);
    }

    /**
     * Mark overdue tasks as expired.
     *
     * @return Collection
     */
    public function markOverdueTasksAsExpired(): Collection
    {
        $overdueTasks = Task::overdue()->get();

        return DB::transaction(function () use ($overdueTasks) {
            foreach ($overdueTasks as $task) {
                $task->status = Task::STATUS_EXPIRED;
                $task->save();

                Log::info("Task #{$task->id} marked as expired (due date: {$task->due_date})");
            }

            return $overdueTasks;
        });
    }
}
