<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// This command will mark overdue tasks as expired
Schedule::command('tasks:expire-overdue')->everyMinute();
