<?php

namespace Asahasrabuddhe\LaravelAPI\Tests\Models;

use Asahasrabuddhe\LaravelAPI\BaseModel;
use Asahasrabuddhe\LaravelAPI\Tests\Http\Resources\PostResource;

class Post extends BaseModel
{
    protected $resource = PostResource::class;

    public function user()
    {
        return $this->belongsTo('Asahasrabuddhe\LaravelAPI\Tests\Models\User');
    }

    public function comments()
    {
        return $this->hasMany('Asahasrabuddhe\LaravelAPI\Tests\Models\Comment');
    }
}
