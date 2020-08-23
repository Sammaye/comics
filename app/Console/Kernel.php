<?php

namespace App\Console;

use App\Console\Commands\ComicEmail;
use App\Console\Commands\ComicScrape;
use App\Console\Commands\DockerRun;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use MongoDB\BSON\ObjectId;

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

        $admin_user = User::query()
            ->where('_id', new ObjectId('544c189d6803fa85038b4567'))
            ->firstOrFail();

        $time = '0 9 * * *';

        if (
            // Has not been sent today
            $admin_user->last_feed_sent->toDateTime()->getTimestamp() < (new Carbon('-1 day'))->getTimestamp() &&
            // Is waaaaay past when it should be sent, like 10am
            (int)(new \DateTime())->format('H') >= 10
        ) {
            // Then schedule it to be sent asap
            if ((int)(new \DateTime())->format('i') !== 0) {
                // If it does not equal 0 minute of the hour then
                // schedule it for the next 0 minute of the next hour
                $hour = (new \DateTime('+1 hour'))->format('G');
            } else {
                // If it is 0 then use this hour now
                $hour = (new \DateTime())->format('G');
            }
            $time = "0 $hour * * *";
        }

        $schedule->command(DockerRun::class)
            ->cron($time)
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
