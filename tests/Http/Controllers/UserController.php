<?php

namespace Asahasrabuddhe\LaravelAPI\Tests\Http\Controllers;

use Asahasrabuddhe\LaravelAPI\BaseController;
use Asahasrabuddhe\LaravelAPI\Tests\Models\User;

class UserController extends BaseController
{
    protected $model = User::class;
}