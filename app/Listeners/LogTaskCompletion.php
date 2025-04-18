<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class LogTaskCompletion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCompleted $event): void
    {
        Log::channel('task_log')->info('Task completed', [
            'task_id' => $event->task->id,
            'task_title' => $event->task->title,
            'completed_at' => now()->toDateTimeString(),
            'completed_by' => auth()->id() ?? 'system',
        ]);
    }


}
