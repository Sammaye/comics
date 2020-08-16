<?php

namespace App\Console\Commands;

class DockerIssue extends DockerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker:issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows you to re-issue tokens to new credentials';

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
        $this->getGoogleService();
    }
}
