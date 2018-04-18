<?php

namespace model_namespace;

use Asahasrabuddhe\LaravelAPI\BaseUser as Authenticatable;
//use App\Http\Resources\model_classResource;
/**
 * Class model_class.
 */
class model_class extends Authenticatable
{
    /**
     * Fully qualified of the Eloquent API Resource class that this model will be transformed into
     *
     * @var string
     */
    //protected $resource = model_classResource::class;

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

    //
}
