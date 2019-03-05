<?php
namespace kr0lik\listFilter\lib;

use Yii;
use yii\base\ErrorException;
use kr0lik\listFilter\Filter;
use kr0lik\listFilter\interfaces\{FilterCollectionInterface, FilterParameterInterface};

trait FilterCollectionTrait
{
    private $parameters = [];

    /**
     * Add parameter to filter
     *
     * $name - Name of parameter in query
     * $type - Type of parameter. DEFAULT checkbox.
     *
     * @param string $id
     * @param string $type Default: Checkbox
     * @return FilterParameterInterface
     * @throws ErrorException
     */
    public function add(string $id, string $type = Filter::PARAMETER_CHECKBOX): FilterParameterInterface
    {
        $this->validateParameterId($id);

        $parameter = $this->makeParameter($id, $type);

        $this->parameters[$id] = $parameter;

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
     * @param $id
     * @return FilterParameter|null
     */
    public function getParameter($id): ?FilterParameterInterface
    {
        return $this->parameters[$id] ?? null;
    }

    /**
     * Delete parameter from filter
     *
     * @param string $id
     * @return Filter
     */
    public function deleteParameter(string $id): FilterCollectionInterface
    {
        unset($this->parameters[$id]);

        return $this;
    }


    public function hasFiltered(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasFiltered()) return true;
        }

        return false;
    }

    public function hasValues(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasValues()) return true;
        }

        return false;
    }

    public function hasSelections(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasSelections()) return true;
        }

        return false;
    }

    public function getSelectedValues(): array
    {
        $selected = [];

        foreach ($this->getParameters() as $parameter) {
            $selected = array_unique(array_merge($selected, $parameter->getSelectedValues()));
        }

        return $selected;
    }



    protected function makeParameter(string $id, string $type): FilterParameterInterface
    {
        $operator = "kr0lik\listFilter\models\FilterParameter$type";

        if (class_exists($operator)) {
            return new $operator($id);
        }

        throw new ErrorException("Unknown parameter type - '{$type}'");
    }

    protected function validateParameterId(string $id): void
    {
        if (! $id) throw new ErrorException('Name cant be empty');
        if (isset($this->parameters[$id])) throw new ErrorException('Parameter $id allready exists');
    }
}
