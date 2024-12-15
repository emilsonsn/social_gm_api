<?php

namespace App\Console\Commands;

use App\Trait\EvolutionTrait;
use Illuminate\Console\Command;

class Teste extends Command
{
    use EvolutionTrait;

    public function __construct() {
        parent::__construct();        
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:teste';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->prepareEvoCredentials();
        $response = $this->fetchInstances();

        $response;
    }
}
