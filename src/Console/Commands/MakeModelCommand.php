<?php

namespace Asahasrabuddhe\LaravelAPI\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Asahasrabuddhe\LaravelAPI\Console\Commands\Creators\ModelCreator;

class MakeModelCommand extends Command
{
    /**
     * The name and signature of the console command
     * 
     * @var string
     */
    protected $signature = 'make:api:model {name} {--auth=false}';

    /**
     * The console command description
     * 
     * @var string
     */
    protected $description = 'Creates a new Eloquent model class with support for Laravel API package.';

    /**
     * @var ModelCreator
     */
    protected $creator;

    /**
     * @var
     */
    protected $composer;

    /**
     * @param ModelCreator $creator
     */
    public function __construct(ModelCreator $creator)
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
        // Set model name.
        $name = $arguments['name'];
        // Is Auth model?
        $auth = $options['auth'];
        // Create the repository.
        if ($this->creator->create($name, $auth)) {
            // Information message.
            $this->info('Successfully created the repository class');
        }
    }
}