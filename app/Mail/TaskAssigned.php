<?php

namespace App\Mail;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

class TaskAssigned extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The task instance.
     *
     * @var \App\Models\Task
     */
    protected $task;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Assigned: ' . $this->task->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Format the due date using Carbon if it exists
        $dueDate = $this->task->due_date ?
            Carbon::parse($this->task->due_date)->format('F j, Y g:i A') :
            'No due date';

        // Get status with proper capitalization
        $status = ucfirst($this->task->status ?? TaskStatus::default()->value);

        // Assign priority based on due date proximity if not explicitly set
        $priority = $this->task->priority ?? $this->calculatePriority();

        return new Content(
            view: 'emails.tasks.assigned',
            with: [
                'task' => $this->task,
                'dueDate' => $dueDate,
                'status' => $status,
                'priority' => $priority,
                'assignedUser' => $this->task->assignedUser ?? null,
                'createdUser' => $this->task->createdUser ?? null,
                'taskUrl' => $this->getTaskUrl(),
            ],
        );
    }

    /**
     * Calculate priority based on due date if not explicitly set.
     *
     * @return string
     */
    protected function calculatePriority(): string
    {
        if (!$this->task->due_date) {
            return 'Normal';
        }

        $dueDate = Carbon::parse($this->task->due_date);
        $now = Carbon::now();
        $daysUntilDue = $now->diffInDays($dueDate, false);

        if ($daysUntilDue < 0) {
            return 'Overdue';
        } elseif ($daysUntilDue <= 2) {
            return 'High';
        } elseif ($daysUntilDue <= 7) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    /**
     * Get the task URL from the _links property or fallback to a default URL.
     *
     * @return string
     */
    protected function getTaskUrl(): string
    {
        if (isset($this->task->_links) && isset($this->task->_links->self) && isset($this->task->_links->self->href)) {
            return $this->task->_links->self->href;
        }

        // Fallback to a default URL pattern
        return url('/tasks/' . $this->task->id);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
