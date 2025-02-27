<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {

        // $schedule->command('tasks:create-scheduled')->everyMinute()->withoutOverlapping();


        // $schedule->command('tasks:create-scheduled')->everyFiveMinutes()->withoutOverlapping(10);
        // // $schedule->command('inspire')->hourly();

        // if (env('APP_ENV') == 'production') {
        //     $schedule->command('tasks:create-scheduled')->lastDayOfMonth('00:00')->hourly()->withoutOverlapping();
        // }

        $schedule->command('tasks:create-inspection-scheduled')->dailyAt('06:00')->withoutOverlapping();
        $schedule->command('tasks:create-drill-scheduled')->dailyAt('06:00')->withoutOverlapping();
        $schedule->command('tasks:create-post-incident-scheduled')->dailyAt('06:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');


        require base_path('routes/console.php');
    }
}
