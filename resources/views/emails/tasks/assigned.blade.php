<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Assigned: {{ $task->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 3px solid #4a7aff;
            margin-bottom: 20px;
        }
        .task-title {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .task-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .task-info {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #5a6268;
        }
        .priority-high, .priority-overdue {
            color: #dc3545;
            font-weight: bold;
        }
        .priority-medium {
            color: #fd7e14;
        }
        .priority-low, .priority-normal {
            color: #28a745;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 14px;
        }
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .status-in-progress {
            background-color: #17a2b8;
            color: white;
        }
        .status-completed {
            background-color: #28a745;
            color: white;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4a7aff;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        .assigned-by {
            font-style: italic;
            margin-bottom: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Task Assignment</h1>
    </div>

    <p>Hello {{ $assignedUser ? $assignedUser['name'] : '' }},</p>
    <p>A task has been assigned to you in the task management system.</p>

    <div class="task-title">{{ $task->title }}</div>

    @if($createdUser && $createdUser->id != ($assignedUser ? $assignedUser->id : 0))
    <div class="assigned-by">
        Assigned by: {{ $createdUser->name }} ({{ $createdUser->email }})
    </div>
    @endif

    <div class="task-details">
        <div class="task-info">
            <span class="label">Description:</span>
            <p>{{ $task->description ?? 'No description provided' }}</p>
        </div>

        <div class="task-info">
            <span class="label">Status:</span>
            <span class="status status-{{ strtolower(str_replace(' ', '-', $status)) }}">{{ $status }}</span>
        </div>

        <div class="task-info">
            <span class="label">Due Date:</span>
            <span>{{ $dueDate }}</span>
        </div>

        <div class="task-info">
            <span class="label">Priority:</span>
            @if(strtolower($priority) == 'high' || strtolower($priority) == 'overdue')
                <span class="priority-high">{{ $priority }}</span>
            @elseif(strtolower($priority) == 'medium')
                <span class="priority-medium">{{ $priority }}</span>
            @else
                <span class="priority-low">{{ $priority }}</span>
            @endif
        </div>

        <div class="task-info">
            <span class="label">Created:</span>
            <span>{{ \Carbon\Carbon::parse($task->created_at)->format('F j, Y g:i A') }}</span>
        </div>

        @if(isset($task->updated_at) && $task->updated_at != $task->created_at)
        <div class="task-info">
            <span class="label">Last Updated:</span>
            <span>{{ \Carbon\Carbon::parse($task->updated_at)->format('F j, Y g:i A') }}</span>
        </div>
        @endif
    </div>

    <a href="{{ $taskUrl }}" class="button">View Task Details</a>

    <p>Please ensure this task is completed by the due date. If you have any questions or concerns, contact your project manager or reply to this notification.</p>

    <p>Thank you,<br>
    The Task Management Team</p>

    <div class="footer">
        <p>This is an automated notification from your task management system.</p>
        <p>© {{ date('Y') }} Your Company. All rights reserved.</p>
    </div>
</body>
</html>
