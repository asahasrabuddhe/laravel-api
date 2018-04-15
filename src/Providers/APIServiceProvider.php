<?php

namespace Asahasrabuddhe\LaravelAPI\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Asahasrabuddhe\LaravelAPI\Routing\BaseRouter;
use Asahasrabuddhe\LaravelAPI\Handlers\ExceptionHandler;
// use Illuminate\Routing\RouteCollection;
// use Illuminate\Routing\Router;
// use Illuminate\Routing\UrlGenerator;
use Asahasrabuddhe\LaravelAPI\Routing\ResourceRegistrar;

class APIServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/api.php' => config_path('api.php'),
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
            __DIR__.'/../config/api.php', 'api'
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
}
