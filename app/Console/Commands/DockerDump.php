<?php

namespace App\Console\Commands;

class DockerDump extends DockerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps the current database and saves it to Google Drive';

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
        $output = null;
        $return_var = null;

        $dumpName = 'dump';
        $dumpPath =  storage_path($dumpName);

        $command = sprintf('mongodump --host=%s --db=%s --archive=%s', 'mongodb', 'comics', $dumpPath);
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            // Error
            exit(1);
        }

        if (!$this->upsertGoogleDriveFile($dumpName, file_get_contents($dumpPath))) {
            // Error
            exit(1);
        }

        exit(0);
    }
}
