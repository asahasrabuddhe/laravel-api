<?php

namespace Asahasrabuddhe\LaravelAPI\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

/**
 * Class ControllerCreator.
 */
class ControllerCreator
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var
     */
    protected $controller;

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Create the controller.
     *
     * @param $controller
     * @param $controller
     * @return int
     */
    public function create($controller)
    {
        // Set the controller.
        $this->setController($controller);
        // Create the directory.
        $this->createDirectory();
        // Return result.
        return $this->createClass();
    }

    protected function createDirectory()
    {
        // Directory.
        $directory = $this->getDirectory();
        // Check if the directory exists.
        if (! $this->files->isDirectory($directory)) {
            // Create the directory if not.
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Get the controller directory.
     *
     * @return mixed
     */
    protected function getDirectory()
    {
        // Get the directory from the config file.
        $directory = app_path();
        // Return the directory.
        return $directory;
    }

    /**
     * Get the populate data.
     *
     * @return array
     */
    protected function getPopulateData()
    {
        // Controller namespace.
        $controller_namespace = Config::get('repositories.controller_namespace');
        // Controller class.
        $controller_class = $this->getControllerName();
        // Populate data.
        $populate_data = [
            'controller_namespace' => $controller_namespace,
            'controller_class'     => $controller_class,
        ];
        // Return populate data.
        return $populate_data;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    protected function getPath()
    {
        // Path.
        $path = $this->getDirectory().DIRECTORY_SEPARATOR.$this->getControllerName().'.php';
        // return path.
        return $path;
    }

    /**
     * Get the stub.
     *
     * @return string
     */
    protected function getStub()
    {
        // Stub
        $stub = $this->files->get($this->getStubPath().'controller.stub.php');
        // Return stub.
        return $stub;
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStubPath()
    {
        // Stub path.
        $stub_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources/stubs/';
        // Return the stub path.
        return $stub_path;
    }

    /**
     * Populate the stub.
     *
     * @return mixed
     */
    protected function populateStub()
    {
        // Populate data
        $populate_data = $this->getPopulateData();
        // Stub
        $stub = $this->getStub();
        // Loop through the populate data.
        foreach ($populate_data as $key => $value) {
            // Populate the stub.
            $stub = str_replace($key, $value, $stub);
        }
        // Return the stub.
        return $stub;
    }

    protected function createClass()
    {
        // Result.
        $result = $this->files->put($this->getPath(), $this->populateStub());
        // Return the result.
        return $result;
    }
}
