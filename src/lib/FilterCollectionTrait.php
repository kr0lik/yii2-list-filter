<?php
namespace kr0lik\listFilter\lib;

use kr0lik\listFilter\exception\ListFilterException;
use kr0lik\listFilter\interfaces\{FilterCollectionInterface, FilterParameterInterface};
use kr0lik\listFilter\FilterParameterTypeEnum;

/**
 * Trait FilterCollectionTrait
 * @package kr0lik\listFilter\lib
 */
trait FilterCollectionTrait
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Add parameter to filter
     *
     * $name - Name of parameter in query
     * $type - Type of parameter. DEFAULT checkbox.
     *
     * @param string $id
     * @param string $type Default: Checkbox
     * @throws ListFilterException
     * @return FilterParameterInterface
     */
    public function add(string $id, string $type = FilterParameterTypeEnum::PARAMETER_CHECKBOX): FilterParameterInterface
    {
        $this->validateParameterId($id);

        $parameter = FilterParameterFabric::create($type, $id);

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
     * @param string|int $id
     * @throws ListFilterException
     * @return FilterParameterInterface
     */
    public function getParameter($id): FilterParameterInterface
    {
        if (! isset($this->parameters[$id])) {
            throw new ListFilterException("Unknown parameter $id");
        }

        return $this->parameters[$id];
    }

    /**
     * Delete parameter from filter
     *
     * @param string $id
     * @return FilterCollectionInterface
     */
    public function deleteParameter(string $id): FilterCollectionInterface
    {
        unset($this->parameters[$id]);

        return $this;
    }


    /**
     * @return bool
     */
    public function hasFiltered(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasFiltered()) return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasValues(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasValues()) return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasSelections(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasSelections()) return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getSelectedValues(): array
    {
        $selected = [];

        foreach ($this->getParameters() as $parameter) {
            $selected = array_unique(array_merge($selected, $parameter->getSelectedValues()));
        }

        return $selected;
    }


    /**
     * @param string $id
     * @throws ListFilterException
     */
    protected function validateParameterId(string $id): void
    {
        if (! $id) throw new ListFilterException('Name cant be empty');
        if (isset($this->parameters[$id])) throw new ListFilterException('Parameter $id allready exists');
    }
}
