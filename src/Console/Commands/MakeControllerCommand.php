<?php

namespace Asahasrabuddhe\LaravelAPI\Console\Commands;

use Illuminate\Console\Command;
use Asahasrabuddhe\LaravelAPI\Console\Commands\Creators\ControllerCreator;

class MakeControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api:controller {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new controller class with support for Laravel API package.';

    /**
     * @var ControllerCreator
     */
    protected $creator;

    /**
     * @var
     */
    protected $composer;

    /**
     * @param ControllerCreator $creator
     */
    public function __construct(ControllerCreator $creator)
    {
        parent::__construct();
        // Set the creator.
        $this->creator = $creator;
        // Set composer.
        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get the arguments.
        $arguments = $this->argument();
        // Write controller.
        $this->writeController($arguments);
        // Dump autoload.
        $this->composer->dumpAutoloads();
    }

    /**
     * @param $arguments
     * @param $options
     */
    protected function writeController($arguments)
    {
        // Set controller.
        $controller = $arguments['name'];
        // Create the controller.
        if ($this->creator->create($controller)) {
            // Information message.
            $this->info('Successfully created the controller class');
        }
    }
}
