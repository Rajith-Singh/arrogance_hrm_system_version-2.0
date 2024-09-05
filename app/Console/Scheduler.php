<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Scheduler extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Schedule the command to run every Monday at 5:00 PM
        $schedule->command('email:weekly-latecomers')->weeklyOn(1, '17:00');
    }

    protected function commands()
    {
        // Load any custom commands
        $this->load(__DIR__.'/Commands');
    }
}
