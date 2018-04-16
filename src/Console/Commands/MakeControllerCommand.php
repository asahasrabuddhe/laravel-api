<?php

namespace Asahasrabuddhe\LaravelAPI\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Asahasrabuddhe\LaravelAPI\Console\Commands\Creators\ControllerCreator;

class MakeControllerCommand extends Command
{
    /**
     * The name and signature of the console command
     * 
     * @var string
     */
    protected $signature = 'make:api:controller';

    /**
     * The console command description
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
        // Get the options.
        $options = $this->option();
        // Write repository.
        $this->writeRepository($arguments, $options);
        // Dump autoload.
        $this->composer->dumpAutoloads();
    }

    /**
     * @param $arguments
     * @param $options
     */
    protected function writeRepository($arguments, $options)
    {
        // Set repository.
        $repository = $arguments['repository'];
        // Set model.
        $model = $options['auth'];
        // Create the repository.
        if ($this->creator->create($repository, $model)) {
            // Information message.
            $this->info('Successfully created the repository class');
        }
    }
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the Controller.'],
        ];
    }
}