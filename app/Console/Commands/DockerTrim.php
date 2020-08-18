<?php

namespace App\Console\Commands;

use App\Comic;
use App\ComicStrip;
use Carbon\Carbon;
use danielme85\LaravelLogToDB\LogToDB;
use Illuminate\Console\Command;

class DockerTrim extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker:trim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A database trimmer that removes all but the last month of data';

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
     * @return mixed
     */
    public function handle()
    {
        $comicStripsDeleted = 0;

        $comics = Comic::query()
            ->get();

        foreach ($comics as $comic) {
            $latest = ComicStrip::query()
                ->select(['_id', 'title', 'created_at'])
                ->where('comic_id', $comic->_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $comicStripsDeleted += ComicStrip::query()
                ->where('created_at', '<', $latest->created_at->subtract('1 month'))
                ->where('comic_id', $comic->_id)
                ->delete();
        }

        $logEntriesDeleted = LogToDB::model(
            null,
            'mongodb',
            config('logging.channels.scraper.collection')
        )
            ->newModelQuery()
            ->where('created_at', '<', new Carbon('-1 month'))
            ->delete();

        $this->info(__(':count comic strips deleted', ['count' => $comicStripsDeleted]));
        $this->info(__(':count logs deleted', ['count' => $logEntriesDeleted]));

        exit(0);
    }
}
