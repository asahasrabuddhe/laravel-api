<?php

namespace Asahasrabuddhe\LaravelAPI\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

/**
 * Class ModelCreator.
 */
class ModelCreator
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var
     */
    protected $model;

    /**
     * @var bool
     */
    protected $auth;

    protected $directorySuffix;

    protected $namespacePrefix;

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
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $path = explode('/', $model);
        if (count($path) > 1) {
            $this->model           = ucfirst(array_pop($path));
            $this->directorySuffix = '/'. implode('/', $path);
        } else {
            $this->model           = ucfirst($path[0]);
            $this->directorySuffix = '';
        }
        $this->namespacePrefix = str_replace('/', '\\', $this->directorySuffix);
    }

    /**
     * @return bool
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param bool $auth
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    /**
     * Create the model.
     *
     * @param $model
     * @param $model
     * @return int
     */
    public function create($model, $auth)
    {
        // Set the model.
        $this->setModel($model);
        // Set Auth.
        $this->setAuth($auth);
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
     * Get the model directory.
     *
     * @return mixed
     */
    protected function getDirectory()
    {
        // Get the directory from the config file.
        $directory = app_path($this->directorySuffix);
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
        // Model namespace.
        $model_namespace = 'App'. $this->namespacePrefix; //Config::get('repositories.model_namespace');
        // Model class.
        $model_class = $this->getModel();
        // Populate data.
        $populate_data = [
            'model_namespace' => $model_namespace,
            'model_class'     => $model_class,
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
        $path = $this->getDirectory().DIRECTORY_SEPARATOR.$this->getModel().'.php';
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
        if ($this->getAuth() == 'true') {
            $stub = $this->files->get($this->getStubPath().'model.auth.stub.php');
        } else {
            $stub = $this->files->get($this->getStubPath().'model.stub.php');
        }
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
