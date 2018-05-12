<?php

namespace Asahasrabuddhe\LaravelAPI\Tests\Http\Controllers;

use Asahasrabuddhe\LaravelAPI\BaseController;
use Asahasrabuddhe\LaravelAPI\Tests\Models\Comment;

class CommentController extends BaseController
{
    protected $model = Comment::class;
}
