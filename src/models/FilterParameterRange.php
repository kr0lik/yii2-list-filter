<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\interfaces\{FilterParameterInterface, FilterParameterValueInterface};
use kr0lik\listFilter\lib\FilterParameterAbstract;

class FilterParameterRange extends FilterParameterAbstract
{
    protected $min = 0;
    protected $max = 0;
    protected $step;
    protected $unit = '';

    public function addValue($value, $name, string $parameterPageUrl = '', string $parameterPageTitle = ''): FilterParameterInterface
    {
        $value = trim($value);

        if (! $this->min || $value < $this->min) {
            $this->min = $key;
        }

        if (! $this->max || $value > $this->max) {
            $this->max = $key;
        }

        if (! $this->step && is_float($value)) {
            $this->step = 0.1;
        }

        return parent::addValue($value, $name, $parameterPageUrl, $parameterPageTitle);
    }

    public function addSelect($select): FilterParameterInterface
    {
        if (is_array($select)) {
            $this->selections = array_merge($this->selections, $select);
        } else {
            $this->selections[] = $select;
        }

        $this->selections = array_unique($this->selections);

        return $this;
    }


    public function setMinValue($min): FilterParameterInterface
    {
        $this->min = $min;

        return $this;
    }

    public function setMaxValue($max): FilterParameterInterface
    {
        $this->max = $max;

        return $this;
    }

    public function getMinValue()
    {
        return $this->min;
    }

    public function getMaxValue()
    {
        return $this->max;
    }

    public function setStep($step): FilterParameterInterface
    {
        $this->step = $step;

        return $this;
    }

    public function getStep()
    {
        return $this->step ?: 1;
    }

    public function hasValues(): bool
    {
        if ($this->getMinValue() !== null || $this->getMaxValue() !== null) {
            return true;
        }

        return parent::hasValues();
    }

    public function setUnit(string $unit): FilterParameterInterface
    {
        $this->unit = $unit;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getInputNameFrom(): string
    {
        return "{$this->getId()}[from]";
    }

    public function getInputNameTo(): string
    {
        return "{$this->getId()}[to]";
    }

    public function getValueFrom()
    {
        $selections = $this->getSelections();

        return isset($selections['from']) ?: $this->getMinValue();
    }

    public function getValueTo()
    {
        $selections = $this->getSelections();

        return isset($selections['to']) ?: $this->getMaxValue();
    }
}
