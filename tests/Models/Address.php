<?php

namespace Asahasrabuddhe\LaravelAPI\Tests\Models;

use Asahasrabuddhe\LaravelAPI\BaseModel;
use Asahasrabuddhe\LaravelAPI\Tests\Http\Resources\AddressResource;

/**
 * Class Address.
 */
class Address extends BaseModel
{
    /**
     * Fully qualified of the Eloquent API Resource class that this model will be transformed into.
     *
     * @var string
     */
    protected $resource = AddressResource::class;

    /**
     * Array of fields that will be included in the default representation of this model in JSON.
     * NOTE: The default preoperty will always be overridden by the resource property.
     *
     * @var array
     */
    // protected $default = [ 'id', attribute1', 'attribute2' ];

    /**
     * Array of fields that can be filtered using the API.
     *
     * @var array
     */
    // protected $filterable = [];

    public function user()
    {
        return $this->belongsTo('Asahasrabuddhe\LaravelAPI\Tests\Models\User');
    }
}
