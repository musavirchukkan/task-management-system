<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskAssignRequest;
use App\Http\Requests\TaskCompleteRequest;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\Interfaces\TaskServiceInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class TaskController extends Controller
{
    /**
     * The task service instance.
     */
    protected TaskServiceInterface $taskService;

    /**
     * Create a new controller instance.
     */
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'status',
                'assigned_to',
                'search',
                'sort_by',
                'sort_direction'
            ]);

            $perPage = $request->input('per_page', 15);

            $tasks = $this->taskService->getTasks($filters, $perPage);

            return response()->json([
                'data' => TaskResource::collection($tasks),
                'message' => 'Tasks retrieved successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve tasks', [
                'exception' => $e->getMessage(),
                'filters' => $filters ?? [],
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'message' => 'Unable to retrieve tasks',
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Store a newly created task.
     */
    public function store(TaskCreateRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;

            $task = $this->taskService->createTask($data);

            return response()->json([
                'message' => 'Task created successfully',
                'task' => new TaskResource($task)
            ], 201);
        } catch (Exception $e) {
            Log::error('Task creation failed', [
                'exception' => $e->getMessage(),
                'data' => $request->except(['_token']),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'message' => 'Failed to create task',
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Assign a task to a user.
     */
    public function assign(TaskAssignRequest $request, int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $userId = $request->validated()['user_id'];
            $task = $this->taskService->assignTask($task, $userId);

            return response()->json([
                'message' => 'Task assigned successfully',
                'task' => new TaskResource($task)
            ]);
        } catch (ModelNotFoundException $e) {
            Log::warning('Task not found during assignment', [
                'task_id' => $id,
                'assigned_by' => $request->user()?->id
            ]);

            return response()->json([
                'message' => 'Task not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('Task assignment failed', [
                'exception' => $e->getMessage(),
                'task_id' => $id,
                'user_id' => $request->validated()['user_id'] ?? null,
                'assigned_by' => $request->user()?->id
            ]);

            return response()->json([
                'message' => 'Failed to assign task',
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Mark a task as completed.
     */
    public function complete(TaskCompleteRequest $request, int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $task = $this->taskService->completeTask($task);

            return response()->json([
                'message' => 'Task completed successfully',
                'task' => new TaskResource($task)
            ]);
        } catch (ModelNotFoundException $e) {
            Log::warning('Task not found during completion', [
                'task_id' => $id,
                'completed_by' => $request->user()?->id
            ]);

            return response()->json([
                'message' => 'Task not found',
            ], 404);
        } catch (InvalidArgumentException $e) {
            Log::warning('Invalid task completion attempt', [
                'exception' => $e->getMessage(),
                'task_id' => $id,
                'completed_by' => $request->user()?->id
            ]);

            return response()->json([
                'message' => 'Cannot complete task',
                'error' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            Log::error('Task completion failed', [
                'exception' => $e->getMessage(),
                'task_id' => $id,
                'completed_by' => $request->user()?->id
            ]);

            return response()->json([
                'message' => 'Failed to complete task',
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Display the specified task.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            return response()->json([
                'task' => new TaskResource($task->load('assignedUser'))
            ]);
        } catch (ModelNotFoundException $e) {
            Log::warning('Task not found during retrieval', [
                'task_id' => $id
            ]);

            return response()->json([
                'message' => 'Task not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('Task retrieval failed', [
                'exception' => $e->getMessage(),
                'task_id' => $id
            ]);

            return response()->json([
                'message' => 'Failed to retrieve task',
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }
}
