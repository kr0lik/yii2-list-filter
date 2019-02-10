<?php
namespace kr0lik\listFilter;

use Yii;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

class Filter
{
    const PARAMETER_DEFAULT = 'checkbox';
    const PARAMETER_BOOLEAN = 'boolean';
    const PARAMETER_RANGE = 'range';
    const PARAMETER_COLLECTION = 'collection';

    /**
     * Filter id
     *
     * @var string
     */
    public $id;

    private $hasValues = false;
    private $filtered = false;
    private $selected = false;

    private $query;
    private static $autoIdPrefix = 'f';
    private static $counter = 0;

    use FilterCollectionTrait;

    public function __construct(ActiveQuery $query)
    {
        $this->query = $query;
        $this->id = static::$autoIdPrefix . static::$counter++;;
    }

    protected function prepare(): void
    {
        foreach ($this->parameters as $parameter) {
            if (Yii::$app->request->getQueryParam($parameter->getName())) $this->filtered = true;

            $parameter->prepare();

            if ($parameter->hasSelections()) $this->selected = true;
            if ($parameter->hasValues()) $this->hasValues = true;
        }
    }

    /**
     * Is any value selected
     *
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->prepare()->selected;
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

    /**
     * Get selected values as array of names
     *
     * @return array
     */
    public function getSelectedValues(): array
    {
        $selected = [];

        foreach ($this->parameters as $parameter) {
            $selected = array_merge($selected, $parameter->getSelectedValues());
        }

        return $selected;
    }

    public function getQuery(): ActiveQuery
    {
        $this->prepare();

        $query = $this->query;

        foreach ($this->getParameters() as $parameter) {
            $select = $parameter->getSelections();

            if ($select !== []) {
                if ($scope = $parameter->getScope()){
                    if (is_string($scope)) {
                        $query->{$scope}($select);
                    } elseif (is_callable($scope)) {
                        ($scope)($query, $select);
                    } else {
                        throw new InvalidConfigException("Bad scope in parameter {$parameter->getName()}");
                    }
                } else {
                    $query->andWhere([$parameter->getName => $select]);
                }
            }
        }

        return $query;
    }
}
