<?php

namespace App\Console;

use Illuminate\Console\Command;

class CreateEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Register the route name in db.';

    /**
     * Create a new command instance.
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
        copy('.env.production', '.env');

        $this->alert('.env created.');
    }
}
