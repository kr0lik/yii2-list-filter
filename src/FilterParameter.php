<?php
namespace kr0lik\listFilter;

use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

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
     * Name of function in ActiveQuery of model OR \Closure
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

    private function assign($collectionId, FilterParameter $parameter): self
    {
        $this->prepared = false;

        $this->collectionId = $collectionId;
        $this->parent = $parameter;

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
     * @param null|int $collectionId
     * @param bool $passAllSelections Passed selections, witch are not in values
     * @return array
     */
    public function getSelections($collectionId = null, bool $passAllSelections = false): array
    {
        $this->prepare();

        if ($this->parent) return $this->parent->getSelections($this->collectionId, $passAllSelections);

        $select = $collectionId ? ($this->select[$collectionId] ?? []) : $this->select;
        $isRange = $collectionId ? (isset($this->collections[$collectionId]) && $this->collections[$collectionId] == self::TYPE_RANGE) : $this->type == self::TYPE_RANGE;

        if ($isRange && $select) {
            $min = $collectionId ? $this->getCollection($collectionId)->min : $this->min;
            $max = $collectionId ? $this->getCollection($collectionId)->max : $this->max;

            if ((isset($select['from']) || isset($select['to']))) {
                if (isset($select['from']) && ! $passAllSelections) {
                    if ($select['from'] < $min || $select['from'] > $max) unset($select['from']);
                }

                if (isset($select['to']) && ! $passAllSelections) {
                    if ($select['to'] < $min || $select['to'] > $max) unset($select['to']);
                }
            } else {
                if (! $passAllSelections) {
                    if (current($select) < $min || current($select) > $max) {
                        $select = [];
                    }
                }

                if ($select) {
                    $select = ['from' => current($select), 'to' => current($select)];
                }
            }
        } elseif (! $passAllSelections) {
            $values = ArrayHelper::getColumn($this->getValues(), 'key');
            $select = array_filter($select, function ($val) use ($values) {
                return in_array($val, $values);
            });
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
                if ($collection->hasValues()) return true;
            }
        }

        return (bool) $this->values || ($this->isRange() && $this->min !== null && $this->max);
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

    /**
     * Get selected values as array of key => name
     *
     * @param bool $passAllSelections Passed selections, witch are not in values
     * @return array
     */
    public function getSelectedValues(bool $passAllSelections = false): array
    {
        $selected = [];

        if ($this->type == self::TYPE_COLLECTION) {
            foreach ($this->collections as $parameter) {
                $selected = array_merge($selected, $parameter->getSelectedValues($passAllSelections));
            }
        } else {
            if ($select = $this->getSelections(null, $passAllSelections)) {
                if (! $this->isRange() && $this->hasValues()) {
                    $values = $this->getValues();
                    $values = ArrayHelper::map($values, 'key', 'name');

                    $values = array_filter($values, function ($key) use ($select) {
                        return in_array($key, $select);
                    }, ARRAY_FILTER_USE_KEY);

                    $select = array_filter($select, function ($val) use ($values) {
                        return ! in_array($val, array_keys($values));
                    });

                    $selected = array_merge(array_values($values), $select);
                } elseif ($this->isRange()) {
                    if (isset($select['from']) && isset($select['to'])) {
                        if ($select['from'] == $select['to']) {
                            $selected[] = "{$select['from']}";
                        } else {
                            $selected[] = "{$select['from']} - {$select['to']}";
                        }
                    } elseif (isset($select['from'])) {
                        $selected[] = "> {$select['from']}";
                    } else {
                        $selected[] = "< {$select['from']}";
                    }
                } else {
                    $selected = $select;
                }
            }

            return array_map(function ($val) {
                return $this->unit ? "$val $this->unit" : $val;
            }, $selected);
        }

        return $selected;
    }
}
