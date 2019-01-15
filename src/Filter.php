<?php
namespace kr0lik\listFilter;

use Yii;
use yii\base\ErrorException;

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
    public function add(string $parameterName, string $parameterTitle, $scope, string $unit = null): FilterParameter
    {
        if (! $parameterName) throw new ErrorException('Name cant be empty');
        //if (! $parameterTitle) throw new ErrorException('Title cant be empty');
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
     * @return FilterParameter|null
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

    /**
     * Get selected values as array of names
     *
     * @param bool $passAllSelections Passed selections, witch are not in values
     * @return array
     */
    public function getSelectedValues(bool $passAllSelections = false): array
    {
        $selected = [];

        foreach ($this->parameters as $parameter) {
            $selected = array_merge($selected, $parameter->getSelectedValues($passAllSelections));
        }

        return $selected;
    }
}
