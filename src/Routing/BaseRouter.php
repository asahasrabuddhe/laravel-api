<?php

namespace Asahasrabuddhe\LaravelAPI\Routing;

use Closure;
use Illuminate\Routing\Router;
use Asahasrabuddhe\LaravelAPI\Middleware\BaseMiddleware;

class BaseRouter extends Router
{
    protected $versions = [];

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return void
     */
    public function resource($name, $controller, array $options = [])
    {
        if ($this->container && $this->container->bound('Asahasrabuddhe\LaravelAPI\Routing\ResourceRegistrar')) {
            $registrar = $this->container->make('Asahasrabuddhe\LaravelAPI\Routing\ResourceRegistrar');
        } else {
            $registrar = new ResourceRegistrar($this);
        }

        $registrar->register($name, $controller, $options);
    }

    public function version($versions, Closure $callback)
    {
        if (is_string($versions)) {
            $versions = [$versions];
        }

        $this->versions = $versions;

        call_user_func($callback, $this);
    }

    /**
     * Add a route to the underlying route collection.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return \Illuminate\Routing\Route
     */
    protected function addRoute($methods, $uri, $action)
    {
        // We do not keep routes in ApiRouter. Whenever a route is added,
        // we add it to Laravel's primary route collection
        $routes = app('router')->getRoutes();
        $prefix = config('api.prefix');

        if (empty($this->versions)) {
            if (($default = config('api.version')) !== null) {
                $versions = [$default];
            } else {
                $versions = [null];
            }
        } else {
            $versions = $this->versions;
        }

        // Add version prefix
        foreach ($versions as $version) {
            // Add Middleware to all routes
            $route = $this->createRoute($methods, $uri, $action);
            $route->middleware(BaseMiddleware::class);

            if ($version !== null) {
                $route->prefix($version);
                $route->name('.' . $version);
            }

            if (! empty($prefix)) {
                $route->prefix($prefix);
            }

            $routes->add($route);

            // Options route
            $route = $this->createRoute(['OPTIONS'], $uri, function () {
                return [];
            });

            $route->middleware(BaseMiddleware::class);

            if ($version !== null) {
                $route->prefix($version);
                $route->name('.' . $version);
            }

            if (! empty($prefix)) {
                $route->prefix($prefix);
            }

            $routes->add($route);
        }

        app('router')->setRoutes($routes);
    }
}
