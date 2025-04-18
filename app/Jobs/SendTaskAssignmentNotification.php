<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTaskAssignmentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The task instance.
     *
     * @var \App\Models\Task
     */
    protected $task;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task, User $user)
    {
        $this->task = $task;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // In a real application, you would send an actual email here
        // For this example, we'll just log it
        Log::info('Task assignment notification sent', [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'user_id' => $this->user->id,
            'user_email' => $this->user->email,
        ]);

        // Simulate email sending
        // Mail::to($this->user->email)->send(new TaskAssigned($this->task));
    }
}
