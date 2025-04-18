<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskAssignRequest;
use App\Http\Requests\TaskCompleteRequest;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only([
            'status',
            'assigned_to',
            'search',
            'sort_by',
            'sort_direction'
        ]);

        $perPage = $request->input('per_page', 15);

        $tasks = $this->taskService->getTasks($filters, $perPage);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created task.
     *
     * @param TaskCreateRequest $request
     * @return JsonResponse
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign a task to a user.
     *
     * @param TaskAssignRequest $request
     * @param int $id
     * @return JsonResponse
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to assign task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a task as completed.
     *
     * @param TaskCompleteRequest $request
     * @param int $id
     * @return JsonResponse
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
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Failed to complete task',
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to complete task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified task.
     *
     * @param  int $id
     * @return TaskResource
     */
    public function show(int $id): TaskResource
    {
        $task = Task::findOrFail($id);
        return new TaskResource($task->load('assignedUser'));
    }
}
