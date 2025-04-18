<?php

namespace App\Jobs;

use App\Mail\TaskAssigned;
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
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @param Task|array $task The task model or an array containing task data
     * @param User|array|null $user The user model or an array containing user data
     */
    public function __construct($task, $user = null)
    {
        // Handle case where task is provided as an array
        if (is_array($task)) {
            $taskModel = new Task();
            foreach ($task as $key => $value) {
                $taskModel->{$key} = $value;
            }
            $this->task = $taskModel;
        } else {
            $this->task = $task;
        }

        // If user is not provided but task has assigned_user, use that
        if (is_null($user) && isset($this->task->assigned_user)) {
            $userModel = new User();
            foreach ($this->task->assigned_user as $key => $value) {
                $userModel->{$key} = $value;
            }
            $this->user = $userModel;
        }
        // Handle case where user is provided as an array
        elseif (is_array($user)) {
            $userModel = new User();
            foreach ($user as $key => $value) {
                $userModel->{$key} = $value;
            }
            $this->user = $userModel;
        }
        // Handle case where user is provided as User model
        else {
            $this->user = $user;
        }

        if (!isset($this->task->assigned_user) && $this->user) {
            $this->task->assigned_user = $this->user;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ensure we have a valid recipient email
        if (!$this->user || !$this->user->email) {
            Log::channel('task_log')->error('Cannot send task assignment notification: No valid recipient', [
                'task_id' => $this->task->id,
                'task_title' => $this->task->title,
            ]);
            return;
        }

        // Log that we're sending the notification
        Log::channel('task_log')->info('Sending task assignment notification', [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'user_id' => $this->user->id,
            'user_email' => $this->user->email,
        ]);

        try {
            // Actually send the email
            Mail::to($this->user->email)
                ->send(new TaskAssigned($this->task));

            // Log success
            Log::channel('task_log')->info('Task assignment notification sent successfully', [
                'task_id' => $this->task->id,
                'user_email' => $this->user->email,
            ]);
        } catch (\Exception $e) {
            // Log failure
            Log::channel('task_log')->error('Failed to send task assignment notification', [
                'task_id' => $this->task->id,
                'user_email' => $this->user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rethrow the exception so Laravel can handle retries
            throw $e;
        }
    }

}
