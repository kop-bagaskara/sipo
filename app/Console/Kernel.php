<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // Send prepress reminders daily at 9:00 AM
        $schedule->command('development:send-prepress-reminders')
                 ->dailyAt('08:00')
                 ->withoutOverlapping();

        $schedule->command('development:send-pic-prepress-reminders')
                 ->dailyAt('08:00')
                 ->withoutOverlapping();

        $schedule->command('development:send-proses-produksi-reminders')
                 ->dailyAt('08:00')
                 ->withoutOverlapping();

        $schedule->command('development:send-job-deadline-fulltime-reminders')
                 ->dailyAt('08:00')
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
