<?php

namespace App\Console;

use App\Console\Commands\ComicEmail;
use App\Console\Commands\ComicScrape;
use App\Console\Commands\DockerRun;
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
        $logPath = '/var/log/crontab.log';

        $schedule->command(ComicScrape::class)
            ->cron('0 9 * * *')
            ->sendOutputTo($logPath, true);

        /*
        $schedule->command(ComicEmail::class)
            ->cron('0 9 * * *');
        */

        $schedule->command(DockerRun::class)
            ->cron('0 10 * * *')
            ->sendOutputTo($logPath, true);
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
