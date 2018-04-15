<?php

namespace Asahasrabuddhe\LaravelAPI\Facades;

use Illuminate\Support\Facades\Facade;
use Asahasrabuddhe\LaravelAPI\Routing\BaseRouter;

class ApiRoute extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseRouter::class;
    }
}
