<?php

namespace Asahasrabuddhe\LaravelAPI\Providers;

use Asahasrabuddhe\LaravelAPI\Handlers\ExceptionHandler;
use Asahasrabuddhe\LaravelAPI\Routing\ResourceRegistrar;
use Asahasrabuddhe\LaravelAPI\Routing\Router;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
// use Illuminate\Routing\RouteCollection;
// use Illuminate\Routing\Router;
// use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../api.php' => config_path("api.php"),
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

        $this->mergeConfigFrom(
            __DIR__.'/../api.php', 'api'
        );
    }

    public function registerRouter()
    {
        $this->app->singleton(
            Router::class,
            function ($app) {
                return new Router($app->make(Dispatcher::class), $app->make(Container::class));
            }
        );

        $this->app->singleton(
            ResourceRegistrar::class,
            function ($app) {
                return new ResourceRegistrar($app->make(Router::class));
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
}