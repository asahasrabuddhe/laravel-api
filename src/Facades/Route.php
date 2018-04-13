<?php

namespace Asahasrabuddhe\LaravelAPI\Facades;

use Asahasrabuddhe\LaravelAPI\Routing\Route;
use Illuminate\Support\Facades\Facade;

class ApiRoute extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Route::class;
    }
}