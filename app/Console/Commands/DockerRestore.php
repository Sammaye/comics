<?php

namespace App\Console\Commands;

class DockerRestore extends DockerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores a database backup from Google Drive';

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

        if (!$this->getGoogleDriveFile($dumpName)) {
            // Error
            exit(1);
        }

        $command = sprintf('mongorestore --host=%s --drop --archive=%s', 'mongodb', $dumpPath);
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            // Error
            exit(1);
        }

        exit(0);
    }
}
