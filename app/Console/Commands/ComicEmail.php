<?php

namespace App\Console\Commands;

use App\Comic;
use App\ComicStrip;
use App\Mail\ComicStrips;
use App\User;
use Carbon\Carbon;
use danielme85\LaravelLogToDB\LogToDB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ComicEmail extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comic:email {frequency?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out comic strip e-mail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param $frequency
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $frequency = $this->argument('frequency');

        $timeToday = new Carbon('today');

        $query = User::query()
            ->orderBy('_id', 'ASC')
            ->where('last_feed_sent', '<', $timeToday)
            ->orWhereNull('last_feed_sent');

        if ($frequency) {
            if (in_array($frequency, ['daily', 'weekly', 'monthly'], true)) {
                $query->where('email_frequency', $frequency);
            } else {
                __('Frequency must be either daily, weekly, or monthly');
            }
        }

        foreach ($query->get() as $user) {
            if (
                $user->last_feed_sent instanceof Carbon
                && $user->last_feed_sent->getTimestamp()
                === $timeToday->getTimestamp()
            ) {
                continue;
            }
            $user->last_feed_sent = $timeToday;

            $strips = [];

            $timeAgo = $timeToday->modify('-1 day');
            if ($user->email_frequency == 'weekly') {
                $timeAgo = $timeToday->modify('1 week');
            } elseif ($user->email_frequency == 'monthly') {
                $timeAgo = $timeToday->modify('-1 month');
            }

            if (!is_array($user->comics)) {
                return false;
            }

            foreach ($user->comics as $sub) {
                if (
                    $comic = Comic::query()
                        ->where('_id', $sub['comic_id'])
                        ->where('live', 1)
                        ->first()
                ) {
                    $comicStrips = ComicStrip::query()
                        ->orderBy('date', 'DESC');

                    $comicStrips->where('comic_id', $comic->_id);

                    if ($comic->active) {
                        $comicStrips->where('date', '>', $timeAgo);
                    } else {
                        $comicStrips->where('index', $comic->current_index);
                    }

                    foreach ($comicStrips->get() as $strip) {
                        $strip->comic = $comic;
                        $strips[] = $strip;
                    }
                }
            }

            if (count($strips) <= 0) {
                // No strips for this user by their settings
                continue;
            }

            $user->save();

            $logEntries = [];
            if ($user->can('root')) {
                $logEntries = LogToDB::model(
                    null,
                    'mongodb',
                    config('logging.channels.scraper.collection')
                )
                    ->newModelQuery()
                    ->where('created_at', '>', new Carbon('today'))
                    ->get();
            }

            Mail::to($user->email)->send(new ComicStrips($strips, $logEntries));
        }
        return 0;
    }
}
