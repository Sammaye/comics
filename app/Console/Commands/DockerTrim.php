<?php

namespace App\Console\Commands;

use App\ComicStrip;
use Carbon\Carbon;
use danielme85\LaravelLogToDB\LogToDB;
use Illuminate\Console\Command;

use MongoDB\BSON\UTCDateTime;

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
        $comicStripsDeleted = ComicStrip::query()
            ->where('created_at', '<', new Carbon('-1 month'))
            ->delete();

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
