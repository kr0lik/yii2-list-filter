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

    public function addValue($key, $value, string $parameterPageUrl = '', string $parameterPageTitle = ''): FilterParameterInterface
    {
        $key = trim($key);

        if (! $this->min || $key < $this->min) {
            $this->min = $key;
        }

        if (! $this->max || $key > $this->max) {
            $this->max = $key;
        }

        if (! $this->step && is_float($key)) {
            $this->step = 0.1;
        }

        return parent::addValue($key, $value, $parameterPageUrl, $parameterPageTitle);
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


    public function setMinKey($min): FilterParameterInterface
    {
        $this->min = $min;

        return $this;
    }

    public function setMaxKey($max): FilterParameterInterface
    {
        $this->max = $max;

        return $this;
    }

    public function getMinKey()
    {
        return $this->min;
    }

    public function getMaxKey()
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
        if ($this->getMinKey() !== null || $this->getMaxKey() !== null) {
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

        return isset($selections['from']) ?: $this->getMinKey();
    }

    public function getValueTo()
    {
        $selections = $this->getSelections();

        return isset($selections['to']) ?: $this->getMaxKey();
    }
}