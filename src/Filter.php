<?php
namespace kr0lik\listFilter;

use Yii;
use yii\base\ErrorException;
use yii\base\Widget;

class Filter
{
    /**
     * If has unit - transform type to range
     *
     * @var bool
     */
    public $autoRangeType = true;

    /**
     * Filter id
     *
     * @var string
     */
    public $id;

    private $parameters = [];
    private $prepared = false;
    private $hasValues = false;
    private $filtered = false;
    private $isSelected = false;

    private static $autoIdPrefix = 'f';
    private static $counter = 0;

    public function __construct()
    {
        $this->id = static::$autoIdPrefix . static::$counter++;;
    }

    /**
     * Add parameter to filter
     *
     * $parameterName - Name of parameter in query
     * $parameterTitle - Title of parameter in View
     * $scope - Name of function in ActiveQuery of model. Must pass variable for selected values.
     * Example:
     * public function scopeName($value) {
     *  return $this->addWhere('field' => $value);
     * }
     * $unit - Unit for values of this parameter
     *
     * @param string $parameterName
     * @param string $parameterTitle
     * @param string $scope
     * @param string|null $unit
     * @return FilterParameter
     * @throws ErrorException
     */
    public function add(string $parameterName, string $parameterTitle, string $scope, string $unit = null): FilterParameter
    {
        if (! $parameterName) throw new ErrorException('Name cant be empty');
        if (! $parameterTitle) throw new ErrorException('Title cant be empty');
        if (! $scope) throw new ErrorException('Scope cant be empty');

        $this->prepared = false;

        $parameter = new FilterParameter();
        $parameter->name = $parameterName;
        $parameter->title = $parameterTitle;
        $parameter->scope = $scope;
        $parameter->unit = $unit;
        $parameter->autoRangeType = $this->autoRangeType;

        if ($parameter->autoRangeType && $parameter->unit) $parameter->type = FilterParameter::TYPE_RANGE;

        $this->parameters[$parameterName] = $parameter;

        return $parameter;
    }

    /**
     * Get all FilterParameter
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get parameter by name
     *
     * @param $name
     * @return mixed|null
     */
    public function getParameter($name)
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Prepare filter
     *
     * @return Filter
     */
    public function prepare(): self
    {
        if (! $this->prepared) {
            $this->prepared = true;

            foreach ($this->parameters as $parameter) {
                if (Yii::$app->request->getQueryParam($parameter->name)) $this->filtered = true;

                $parameter->prepare();

                if ($parameter->isSelected()) $this->isSelected = true;
                if ($parameter->hasValues()) $this->hasValues = true;
            }
        }

        return $this;
    }

    /**
     * Is any value selected
     *
     * @return bool
     */
    public function isSelected(): bool
    {
        return (bool) $this->prepare()->isSelected;
    }

    /**
     * Has come any parameter from query
     *
     * @return bool
     */
    public function isFiltered(): bool
    {
        return (bool) $this->prepare()->filtered;
    }

    /**
     * Is filter has any value
     *
     * @return bool
     */
    public function hasValues(): bool
    {
        return (bool) $this->prepare()->hasValues;
    }
}

class FilterParameter
{
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RANGE = 'range';
    const TYPE_COLLECTION = 'collection';

    /**
     * Name of parameter
     *
     * @var string
     */
    public $name;

    /**
     * Title of parameter
     *
     * @var string
     */
    public $title;

    /**
     * Unit of parameter values
     *
     * @var string
     */
    public $unit;

    /**
     * Type of parameter values view
     * Default: self::TYPE_CHECKBOX
     *
     * @var string
     */
    public $type = self::TYPE_CHECKBOX;

    /**
     * If has unit - transform type to range
     *
     * @var bool
     */
    public $autoRangeType = true;

    /**
     * Name of function in ActiveQuery of model
     *
     * @var string
     */
    public $scope;

    /**
     * If type id range: min value
     *
     * @var float
     */
    public $min;

    /**
     * If type id range: max value
     *
     * @var float
     */
    public $max;

    /**
     * If type id range: step for slider
     *
     * @var float
     */
    public $step = 0.1;

    private $collectionId;
    private $parent;
    private $collections = [];
    private $select = [];
    private $values = [];
    private $prepared = false;

    /**
     * Add value to this parameter
     * $key - value for added value
     * $name - name for added value
     * $url - for linked label of added value. If null - label ni linked
     * $title - for label of added value. Id null - will get name
     *
     * @param string $key
     * @param string $name
     * @param string | null $title
     * @param string | null $url
     * @return FilterParameter
     * @throws ErrorException
     */
    public function addValue($key, string $name, string $url = null, string $title = null): self
    {
        $this->prepared = false;

        if ($this->type != self::TYPE_COLLECTION) {
            $value = new \stdClass();
            $value->key = $key;
            $value->name = $name;
            $value->url = $url;
            $value->title = $title;

            $this->values[$key] = $value;

            if ($this->type == self::TYPE_RANGE) {
                $this->min = $this->min === null || (float) $value->key < $this->min ? (float) $value->key : $this->min;
                $this->max = $this->max === null || (float) $value->key > $this->max ? (float) $value->key : $this->max;
            }

            return $this;
        } else {
            throw new ErrorException('Cant add value to collection type. You mast get collection and there add value.');
        }
    }

    /**
     * Get all values as stdClasses
     * $sort - Sort values by name before return array
     *
     * @param bool $sort
     * @return array
     */
    public function getValues(bool $sort = false): array
    {
        $values = $this->values;

        if ($sort) {
            $values = [];
            foreach ($this->values as $value) {
                $name = mb_strtolower($value->name);
                $value->name = mb_strtoupper(mb_substr($name, 0, 1)) . mb_substr($name, 1);
                $values[$name] = $value;
            }
            ksort($values, SORT_NATURAL);
            $values = array_values($values);
        }

        return $values;
    }

    /**
     * Make grouped parameter
     *
     * @param $collectionId
     * @param string $title
     * @param string | null $unit
     * @return FilterParameter
     */
    public function addCollection($collectionId, string $title, string $unit = null): FilterParameter
    {
        $this->prepared = false;

        $this->type = self::TYPE_COLLECTION;

        $collection = $this->getCollection($collectionId) ?: new FilterParameter();
        $collection->title = $title;
        $collection->unit = $unit;
        $collection->autoRangeType = $this->autoRangeType;

        if ($collection->autoRangeType && $collection->unit) $collection->type = self::TYPE_RANGE;

        $collection->assign($collectionId, $this);

        $this->collections[$collectionId] = $collection;

        return $collection;
    }

    private function assign($collectionId, FilterParameter $parent): self
    {
        $this->prepared = false;

        $this->collectionId = $collectionId;
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get all collections
     *
     * @return array
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    /**
     * Get collection by id
     *
     * @param $collectionId
     * @return mixed|null
     */
    public function getCollection($collectionId)
    {
        return $this->collections[$collectionId] ?? null;
    }

    /**
     * Add selected value
     *
     * @param $value
     * @param null $collectionId
     * @return FilterParameter
     */
    public function addSelect($value, $collectionId = null): self
    {
        if ($value) {
            if ($this->parent) {
                $this->parent->addSelect($value, $collectionId);
            } else {
                if ($collectionId) $this->type = self::TYPE_COLLECTION;

                if (is_array($value)) {
                    $this->select += $value;
                } elseif ($this->type == self::TYPE_COLLECTION) {
                    $this->select[$collectionId][]  = $value;
                } else {
                    $this->select[]  = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Get selected values
     *
     * @param null $collectionId
     * @return array
     */
    public function getSelections($collectionId = null): array
    {
        $this->prepare();

        if ($this->parent) return $this->parent->getSelections($this->collectionId);

        $select = $collectionId ? ($this->select[$collectionId] ?? []) : $this->select;
        $isRange = $collectionId ? (isset($this->collections[$collectionId]) && $this->collections[$collectionId] == self::TYPE_RANGE) : $this->type == self::TYPE_RANGE;

        if ($isRange && $select) {
            if (isset($select['from']) || isset($select['to'])) return $select;

            return ['from' => current($select), 'to' => current($select)];
        }

        return $select;
    }

    /**
     * Prepare parameter
     *
     * @return FilterParameter
     */
    public function prepare()
    {
        if (! $this->prepared) {
            $this->prepared = true;

            if ($select = Yii::$app->request->getQueryParam($this->name)) {
                $this->addSelect($select);
            }
        }

        return $this;
    }

    /**
     * Get current collectionId
     *
     * @return mixed
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Is this parameter is collection
     *
     * @return bool
     */
    public function isCollection(): bool
    {
        return $this->type == self::TYPE_COLLECTION;
    }

    /**
     * Is this parameter is range
     *
     * @return bool
     */
    public function isRange(): bool
    {
        return $this->type == self::TYPE_RANGE;
    }

    /**
     * Is this parameter is checkbox
     *
     * @return bool
     */
    public function isCheckbox(): bool
    {
        return $this->type == self::TYPE_CHECKBOX;
    }

    /**
     * Is this parameter has any selected values
     *
     * @return bool
     */
    public function isSelected(): bool
    {
        return (bool) $this->getSelections();
    }

    /**
     * Is this parameter has any values
     *
     * @return bool
     */
    public function hasValues(): bool
    {
        if ($this->type == self::TYPE_COLLECTION) {
            foreach ($this->collections as $collection) {
                if ($collection->getValues()) return true;
                if ($collection->isRange()) return true;
            }
        }

        return (bool) $this->values || $this->isRange();
    }

    /**
     * Parameter name for input field
     *
     */
    public function getInputName(): string
    {
        return $this->getCollectionId() ? "{$this->parent->name}[{$this->getCollectionId()}]" : $this->name;
    }

    /**
     * Parameter id
     *
     */
    public function getId(): string
    {
        return preg_replace('/[^\w\-]/', '-', $this->getInputName());
    }
}
