<?php

namespace Asahasrabuddhe\LaravelAPI;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Asahasrabuddhe\LaravelAPI\Helpers\ReflectionHelper;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\UnknownFieldException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidOffsetException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidPerPageLimitException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\FieldCannotBeFilteredException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidFilterDefinitionException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidOrderingDefinitionException;

class RequestParser
{
    /**
     * Checks if fields are specified correctly.
     */
    const FIELDS_REGEX = '/([a-zA-Z0-9\\_\\-\\:\\.\\(\\)]+(\\{((?>[^{}]+)|)*\\})?+)/';
    /**
     * Extracts fields parts.
     */
    const FIELD_PARTS_REGEX = '/([^{.]+)(.limit\\(([0-9]+)\\)|.offset\\(([0-9]+)\\)|.order\\(([A-Za-z_]+)\\))*(\\{((?>[^{}]+)|(?R))*\\})?/i';
    /**
     * Checks if filters are correctly specified.
     */
    const FILTER_REGEX = '/(\\((?:[\\s]*(?:and|or)?[\\s]*[\\w\\.]+[\\s]+(?:(\beq\b)|(\bne\b)|(\bgt\b)|(\bge\b)|(\blt\b)|(\ble\b)|(\blk\b))[\\s]+(?:\\"(?:[^\\"\\\\]|\\\\.)*\\"|\\d+(,\\d+)*(\\.\\d+(e\\d+)?)?|null)[\\s]*|(?R))*\\))/i';
    /**
     * Extracts filter parts.
     */
    const FILTER_PARTS_REGEX = '/([\\w\\.]+)[\\s]+(?:(\beq\b)|(\bne\b)|(\bgt\b)|(\bge\b)|(\blt\b)|(\ble\b)|(\blk\b))[\\s]+(?:"(?:[^"\\\\]|\\\\.)*"|\\d+(?:,\\d+)*(?:\\.\\d+(?:e\\d+)?)?|null)/i';
    /**
     * Checks if ordering is specified correctly.
     */
    const ORDER_FILTER = '/[\\s]*([\\w\\.]+)(?:[\\s](?!,))*(asc|desc)/i';

    /**
     * Extract order parts for relational field.
     */
    const RELATION_ORDER_REGEX = '/[\\s]*([\\w]+)\\.([\\w]+)(?:[\\s](?!,))*(asc|desc)/';

    // /**
    //  * Extract order parts for regular field
    //  */
    // const ORDER_REGEX = "/[\\s]*([\\w`\\.]+)(?:[\\s](?!,))*(asc|desc|)/";

    const OPERATOR_REGEX = '/[\\s]+|(\beq\b)|(\bne\b)|(\bgt\b)|(\bge\b)|(\blt\b)|(\ble\b)|(\blk\b)|[\\s]+/i';

    const NULL_NOT_NULL_REGEX = '/(ne|eq)[\\s]+(null)/i';

    const RELATION_FILTER_REGEX = '/([\\w]+)\\.([\\w]+)[\\s]+((\beq\b)|(\bne\b)|(\bgt\b)|(\bge\b)|(\blt\b)|(\ble\b)|(\blk\b))/i';

    const REGULAR_FILTER_REGEX = '/([\\w]+)[\\s]+((\beq\b)|(\bne\b)|(\bgt\b)|(\bge\b)|(\blt\b)|(\ble\b)|(\blk\b))/i';

    /**
     * Full class reference to the model represented in this request.
     *
     * @var string
     */
    protected $model = null;

    /**
     * Table name corresponding to the model.
     *
     * @var string
     */
    private $table = null;

    /**
     * Primary key of the model.
     *
     * @var string
     */
    private $primaryKey = null;

    /**
     * Fields to be returned in response. This does not include relations.
     *
     * @var array
     */
    private $fields = [];

    /**
     * Relations to be included in the response.
     *
     * @var array
     */
    private $relations = [];

    /**
     * Number of results requested per page.
     *
     * @var int
     */
    private $limit = 10;

    /**
     * Offset from where fetching should start.
     *
     * @var int
     */
    private $offset = 0;

    /**
     * Ordering string.
     *
     * @var int
     */
    private $order = null;

    /**
     * Filters to be applied.
     *
     * @var string
     */
    private $filters = null;

    /**
     * Attributes passed in request.
     *
     * @var array
     */
    private $attributes = [];

    /**
     * RequestParser constructor.
     * @param $model
     * @throws \Exception
     */
    public function __construct($model)
    {
        $this->model      = $model;
        $this->primaryKey = call_user_func([new $this->model(), 'getKeyName']);
        try {
            $this->parseRequest();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param array $relations
     */
    public function setRelations($relations)
    {
        $this->relations = $relations;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Parse request and fill the parameters.
     *
     * @return $this current controller object for chain method calling
     * @throws InvalidPerPageLimitException
     * @throws InvalidOffsetException
     * @throws InvalidFilterDefinitionException
     * @throws InvalidOrderingDefinitionException
     * @throws FieldCannotBeFilteredException
     */
    protected function parseRequest()
    {
        if (isset(request()->limit)) {
            if (request()->limit <= 0) {
                throw new InvalidPerPageLimitException;
            }
            $this->limit = request()->limit;
        } else {
            $this->limit = config('api.perPage');
        }

        if (isset(request()->offset)) {
            if (request()->offset < 0) {
                throw new InvalidOffsetException;
            }
            $this->offset = request()->offset;
        } else {
            $this->offset = 0;
        }

        $this->extractFields();
        $this->extractFilters();
        $this->extractOrdering();
        $this->loadTableName();

        $this->attributes = request()->all();

        return $this;
    }

    /**
     * @throws UnknownFieldException
     */
    protected function extractFields()
    {
        if (request()->fields) {
            $this->parseFields(request()->fields);
        } elseif (null === call_user_func($this->model . '::getResource')) {
            // Else, by default, we only return default set of visible fields
            $fields = call_user_func($this->model . '::getDefaultFields');
            // We parse the default fields in same way as above so that, if
            // relations are included in default fields, they also get included
            $this->parseFields(implode(',', $fields));
        } else {
            // Fully qualified name of the API Resource
            $className = call_user_func($this->model . '::getResource');
            // Reflection Magic
            $reflection = new ReflectionHelper($className);
            // Get list of fields from Resource
            $fields = $reflection->getFields();
            // parse extracted fields
            $this->parseFields(implode(',', $fields));
        }
        if (! in_array($this->primaryKey, $this->fields)) {
            $this->fields[] = $this->primaryKey;
        }
    }

    /**
     * @throws FieldCannotBeFilteredException
     * @throws InvalidFilterDefinitionException
     */
    protected function extractFilters()
    {
        if (request()->filters) {
            $filters = '(' . request()->filters . ')';
            if (preg_match(static::FILTER_REGEX, $filters) === 1) {
                preg_match_all(static::FILTER_PARTS_REGEX, $filters, $parts);
                $filterable = call_user_func($this->model . '::getFilterableFields');
                foreach ($parts[1] as $column) {
                    if (! in_array($column, $filterable)) {
                        throw new FieldCannotBeFilteredException('Applying filter on field "' . $column . '" is not allowed');
                    }
                }
                // Convert filter name to sql `column` format
                $where = preg_replace_callback_array([
                        static::RELATION_FILTER_REGEX => [$this, 'formatRelationalFilteredFieldToSql'],
                        static::REGULAR_FILTER_REGEX  => [$this, 'formatFilteredFieldToSql'],
                    ], $filters);
                // convert eq null to is null and ne null to is not null
                $where = preg_replace_callback_array([
                        static::NULL_NOT_NULL_REGEX => [$this, 'formatNullAndNotNull'],
                    ], $where);
                // Replace operators
                $where = preg_replace_callback_array([
                    static::OPERATOR_REGEX => [$this, 'replaceOperators'],
                ], $where);
                $this->filters = $where;
            } else {
                throw new InvalidFilterDefinitionException();
            }
        }
    }

    /**
     * Formats relational field in SQL format.
     *
     * @param array $matches
     * @return string
     */
    protected function formatRelationalFilteredFieldToSql($matches)
    {
        return '`' . $matches[1] . '_' . $matches[2] . '` ' . $matches[3];
    }

    /**
     * Formats field in SQL format.
     *
     * @param array $matches
     * @return string
     */
    protected function formatFilteredFieldToSql($matches)
    {
        return '`' . $matches[1] . '` ' . $matches[2];
    }

    protected function formatNullAndNotNull($matches)
    {
        switch ($matches[1]) {
            case 'ne':
                return 'is not ' . $matches[2];
            case 'eq':
                return 'is ' . $matches[2];
            default:
                return $matches[1];
        }
    }

    protected function replaceOperators($matches)
    {
        switch (trim($matches[0])) {
            case 'eq':
                return' = ';
            case 'ne':
                return' <> ';
            case 'gt':
                return' > ';
            case 'ge':
                return' >= ';
            case 'lt':
                return' < ';
            case 'le':
                return' <= ';
            case 'lk':
                return' LIKE ';
            case 'nl':
                return' NOT LIKE ';
        default:
                return $matches[0];
       }
    }

    /**
     * @throws InvalidOrderingDefinitionException
     */
    protected function extractOrdering()
    {
        if (request()->order) {
            if (preg_match(static::ORDER_FILTER, request()->order) === 1) {
                $order = request()->order;
                $order = preg_replace_callback_array([
                    static::RELATION_ORDER_REGEX => [$this, 'formatRelationalOrderingFieldToSql'],
                    static::ORDER_FILTER         => [$this, 'formatOrderingFieldToSql'],
                    ], $order);
                $this->order = $order;
            } else {
                throw new InvalidOrderingDefinitionException;
            }
        }
    }

    /**
     * Formats relational field in SQL format
     * eg. user.id desc, ->  user`.`id desc.
     *
     * @param array $matches
     * @return string
     */
    protected function formatRelationalOrderingFieldToSql($matches)
    {
        return '`' . $matches[1] . '_' . $matches[2] . '` ' . $matches[3];
    }

    /**
     * Formats field in SQL format
     * eg. id asc, ->  `id` asc.
     *
     * @param array $matches
     * @return string
     */
    protected function formatOrderingFieldToSql($matches)
    {
        return '`' . $matches[1] . '` ' . $matches[2];
    }

    /**
     * Recursively parses fields to extract limit, ordering and their own fields
     * and adds width relations.
     *
     * @param $fields
     * @throws UnknownFieldException
     */
    private function parseFields($fields)
    {
        // If fields parameter is set, parse it using regex
        preg_match_all(static::FIELDS_REGEX, $fields, $matches);
        if (! empty($matches[0])) {
            foreach ($matches[0] as $match) {
                preg_match_all(static::FIELD_PARTS_REGEX, $match, $parts);
                $fieldName = $parts[1][0];
                if (Str::contains($fieldName, ':') || call_user_func($this->model . '::relationExists', $fieldName)) {
                    // If field name has a colon, we assume its a relations
                    // OR
                    // If method with field name exists in the class, we assume its a relation
                    // This is default laravel behavior
                    $limit  = ($parts[3][0] == '') ? config('api.perPage') : $parts[3][0];
                    $offset = ($parts[4][0] == '') ? 0 : $parts[4][0];
                    $order  = ($parts[5][0] == 'asc') ? 'asc' : 'desc';
                    if (! empty($parts[7][0])) {
                        $subFields = explode(',', $parts[7][0]);
                        // This indicates if user specified fields for relation or not
                        $userSpecifiedFields = true;
                    } else {
                        $subFields           = [];
                        $userSpecifiedFields = false;
                    }
                    $fieldName = str_replace(':', '.', $fieldName);
                    if (! isset($this->relations[$fieldName])) {
                        $this->relations[$fieldName] = [
                            'limit'               => $limit,
                            'offset'              => $offset,
                            'order'               => $order,
                            'fields'              => $subFields,
                            'userSpecifiedFields' => $userSpecifiedFields,
                        ];
                    } else {
                        $this->relations[$fieldName]['limit']  = $limit;
                        $this->relations[$fieldName]['offset'] = $offset;
                        $this->relations[$fieldName]['order']  = $order;
                        $this->relations[$fieldName]['fields'] = array_merge($this->relations[$fieldName]['fields'], $subFields);
                    }
                    // We also need to add the relation's foreign key field to select. If we don't,
                    // relations always return null
                    if (Str::contains($fieldName, '.')) {
                        $relationNameParts = explode('.', $fieldName);
                        $model             = $this->model;
                        $relation          = null;
                        foreach ($relationNameParts as $rp) {
                            $relation = call_user_func([new $model(), $rp]);
                            $model    = $relation->getRelated();
                        }
                        // Its a multi level relations
                        $fieldParts = explode('.', $fieldName);
                        if ($relation instanceof BelongsTo) {
                            $singular = $relation->getForeignKey();
                        } elseif ($relation instanceof HasOne || $relation instanceof HasMany) {
                            $singular = explode('.', $relation->getForeignKey())[1];
                        }
                        // Unset last element of array
                        unset($fieldParts[count($fieldParts) - 1]);
                        $parent = implode('.', $fieldParts);
                        if ($relation instanceof HasOne || $relation instanceof HasMany) {
                            // For hasMany and HasOne, the foreign key is in current relation table, not in parent
                            $this->relations[$fieldName]['fields'][] = $singular;
                        } else {
                            // The parent might already been set because we cannot rely on order
                            // in which user sends relations in request
                            if (! isset($this->relations[$parent])) {
                                $this->relations[$parent] = [
                                    'limit'               => config('api.perPage'),
                                    'offset'              => 0,
                                    'order'               => 'chronological',
                                    'fields'              => isset($singular) ? [$singular] : [],
                                    'userSpecifiedFields' => true,
                                ];
                            } else {
                                if (isset($singular)) {
                                    $this->relations[$parent]['fields'][] = $singular;
                                }
                            }
                        }
                        if ($relation instanceof BelongsTo) {
                            $this->relations[$fieldName]['limit'] = max($this->relations[$fieldName]['limit'], $this->relations[$parent]['limit']);
                        } elseif ($relation instanceof HasMany) {
                            $this->relations[$fieldName]['limit'] = $this->relations[$fieldName]['limit'] * $this->relations[$parent]['limit'];
                        }
                    } else {
                        $relation = call_user_func([new $this->model(), $fieldName]);

                        switch (true) {
                            case $relation instanceof HasOne:
                                $keyField = explode('.', $relation->getQualifiedParentKeyName())[1];
                                break;
                            case $relation instanceof BelongsTo:
                                $keyField                             = explode('.', $relation->getQualifiedForeignKey())[1];
                                $this->relations[$fieldName]['limit'] = max($this->relations[$fieldName]['limit'], $this->limit);
                                break;
                            case $relation instanceof HasMany:
                                $this->relations[$fieldName]['limit'] = $this->relations[$fieldName]['limit'] * $this->limit;
                        }

                        if (isset($keyField) && ! in_array($keyField, $this->fields)) {
                            $this->fields[] = $keyField;
                        }
                    }
                } else { // Else, its a normal field
                    // Check if the field actually exists otherwise, throw exception
                    if (Schema::hasColumn((new $this->model())->getTable(), $fieldName)) {
                        $this->fields[] = $fieldName;
                    } else {
                        throw new UnknownFieldException;
                    }
                }
            }
        }
    }

    /**
     * Load table name into the $table property.
     */
    private function loadTableName()
    {
        $this->table = call_user_func($this->model . '::getTableName');
    }
}
