<?php

namespace Asahasrabuddhe\LaravelAPI\Tests\Http\Controllers;

use Asahasrabuddhe\LaravelAPI\BaseController;
use Asahasrabuddhe\LaravelAPI\Tests\Models\Post;

class PostController extends BaseController
{
    protected $model = Post::class;
}