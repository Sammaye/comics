<?php

namespace App\Console\Commands;

use App\Comic;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ComicScrape extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comic:scrape {id?} {force=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape comic strips';

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
     * @param $id
     * @param $force
     *
     * @return mixed
     */
    public function handle($id, $force)
    {
        $query = Comic::query();

        if ($id) {
            $query->where('_id', $id)
                ->orWhere('title', $id);

            if ($query->count() <= 0) {
                throw (new ModelNotFoundException)->setModel(new Comic, $id);
            }
        }

        $comics = $query->get();

        foreach($comics as $comic) {
            $comic->scrapeCron($force);
        }

        return 0;
    }
}
