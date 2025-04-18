<?php

namespace App\Console\Commands;

use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Console\Command;

class ExpireOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:expire-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark overdue tasks as expired';

    /**
     * Execute the console command.
     */
    public function handle(TaskServiceInterface $taskService): int
    {
        $this->info('Starting to check for overdue tasks...');

        $expiredTasks = $taskService->markOverdueTasksAsExpired();

        $count = $expiredTasks->count();

        $this->info("Task expiration check completed. {$count} tasks marked as expired.");

        return Command::SUCCESS;
    }
}
