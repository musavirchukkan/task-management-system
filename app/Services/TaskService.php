<?php

namespace App\Services;

use App\Enums\TaskStatus;
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
        $task = DB::transaction(function () use ($data) {
            return Task::create($data);
        });

        if (isset($data['assigned_to'])) {
            // Check if user exists
            $user = User::find($data['assigned_to']);
        }

        $task->load('assignedUser', 'createdUser');

        if (isset($user)) {
            // Dispatch job to send notification
            SendTaskAssignmentNotification::dispatch($task, $user);
        }
        return $task;
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

            $task->load('assignedUser', 'createdUser');
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
        return DB::transaction(function () use ($task) {
            $task->status = TaskStatus::COMPLETED->value;
            $task->completed_at = now();
            $task->save();

            // Trigger task completed event
            event(new TaskCompleted($task));

            $task->load('assignedUser', 'createdUser');

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
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (isset($filters['due_date'])) {
            $query->whereDate('due_date', $filters['due_date']);
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

        return $query->with(['assignedUser', 'createdUser'])->paginate($perPage);
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
                $task->status = TaskStatus::EXPIRED->value;
                $task->save();

                Log::channel('task_log')->info("Task #{$task->id} marked as expired (due date: {$task->due_date})");
            }

            return $overdueTasks;
        });
    }
}
