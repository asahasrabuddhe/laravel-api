<?php

namespace Asahasrabuddhe\LaravelAPI\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Asahasrabuddhe\LaravelAPI\Routing\BaseRouter;
use Asahasrabuddhe\LaravelAPI\Handlers\ExceptionHandler;
use Asahasrabuddhe\LaravelAPI\Routing\ResourceRegistrar;
use Asahasrabuddhe\LaravelAPI\Console\Commands\MakeModelCommand;
use Asahasrabuddhe\LaravelAPI\Console\Commands\MakeControllerCommand;
use Asahasrabuddhe\LaravelAPI\Console\Commands\Creators\ModelCreator;
use Asahasrabuddhe\LaravelAPI\Console\Commands\Creators\ControllerCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class APIServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/api.php' => config_path('api.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
        $this->registerExceptionHandler();
        $this->registerBindings();

        $this->commands([MakeModelCommand::class, MakeControllerCommand::class]);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/api.php',
            'api'
        );
    }

    public function registerRouter()
    {
        $this->app->singleton(
            BaseRouter::class,
            function ($app) {
                return new BaseRouter($app->make(Dispatcher::class), $app->make(Container::class));
            }
        );

        $this->app->singleton(
            ResourceRegistrar::class,
            function ($app) {
                return new ResourceRegistrar($app->make(BaseRouter::class));
            }
        );
    }

    public function registerExceptionHandler()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            ExceptionHandler::class
        );
    }

    public function registerBindings()
    {
         // FileSystem.
        $this->app->instance('FileSystem', new Filesystem());
         // Composer.
        $this->app->bind('Composer', function ($app) {
            return new Composer($this->app->make('FileSystem'));
        });
         // ModelCreator creator.
        $this->app->singleton('ModelCreator', function ($app) {
            return new ModelCreator($this->app->make('FileSystem'));
        });
         // ControllerCreator creator.
        $this->app->singleton('ControllerCreator', function ($app) {
            return new ControllerCreator($this->app->make('FileSystem'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.repository.make',
            'command.rule.make',
        ];
    }

}
