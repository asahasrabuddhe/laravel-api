<?php

namespace Asahasrabuddhe\LaravelAPI\Tests\Http\Controllers;

use Asahasrabuddhe\LaravelAPI\BaseController;
use Asahasrabuddhe\LaravelAPI\Tests\Models\Address;

class AddressController extends BaseController
{
    protected $model = Address::class;
}
