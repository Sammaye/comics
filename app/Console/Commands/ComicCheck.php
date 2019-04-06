<?php

namespace App\Console\Commands;

use App\Comic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ComicCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comic:check {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check when a comic is updated';

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
     *
     * @return mixed
     */
    public function handle($id)
    {
        $comic = Comic::query()->where('_id', $id)->findOrFail();

        $strip = $comic->current();
        $index = $comic->next(
            $strip,
            true
        )->index;

        while (true) {
            if ($comic->indexExist($index)) {
                Log::info(
                    __(
                        'Index :index exists',
                        [
                            'index' => $comic->type === self::TYPE_DATE
                                ? $index->format('d-m-Y')
                                : $index
                        ]
                    )
                );
                return 0;
            }
            sleep(3600);
        }
    }
}
