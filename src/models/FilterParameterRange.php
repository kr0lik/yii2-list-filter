<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\FilterParameterTypeEnum;
use kr0lik\listFilter\interfaces\FilterParameterInterface;
use kr0lik\listFilter\lib\FilterParameterAbstract;

/**
 * Class FilterParameterRange
 * @package kr0lik\listFilter\models
 */
class FilterParameterRange extends FilterParameterAbstract
{
    /**
     * @var int|float
     */
    protected $min = 0;
    /**
     * @var int|float
     */
    protected $max = 0;
    /**
     * @var int|float
     */
    protected $step;
    /**
     * @var string
     */
    protected $unit = '';

    /**
     * @return string
     */
    public static function getType(): string
    {
        return FilterParameterTypeEnum::PARAMETER_RANGE;
    }

    /**
     * @param mixed $value
     * @param null $name
     * @param string|null $parameterPageUrl
     * @param string|null $parameterPageTitle
     * @return FilterParameterInterface
     */
    public function addValue($value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null): FilterParameterInterface
    {
        $value = is_string($value) ? trim($value) : $value;

        if (! $this->min || $value < $this->min) {
            $this->min = $value;
        }

        if (! $this->max || $value > $this->max) {
            $this->max = $value;
        }

        if (! $this->step && is_float($value)) {
            $this->step = 0.1;
        }

        return parent::addValue($value, $name, $parameterPageUrl, $parameterPageTitle);
    }

    /**
     * @param mixed $select
     * @return FilterParameterInterface
     */
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


    /**
     * @param int|float $min
     * @return FilterParameterInterface
     */
    public function setMinValue($min): FilterParameterInterface
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @param int|float $max
     * @return FilterParameterInterface
     */
    public function setMaxValue($max): FilterParameterInterface
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return int|float
     */
    public function getMinValue()
    {
        return $this->min;
    }

    /**
     * @return int|float
     */
    public function getMaxValue()
    {
        return $this->max;
    }

    /**
     * @param int|float $step
     * @return FilterParameterInterface
     */
    public function setStep($step): FilterParameterInterface
    {
        $this->step = $step;

        return $this;
    }

    /**
     * @return int|float
     */
    public function getStep()
    {
        return $this->step ?: 1;
    }

    /**
     * @return bool
     */
    public function hasValues(): bool
    {
        if ($this->getMinValue() > 0 || $this->getMaxValue() > 0) {
            return true;
        }

        return parent::hasValues();
    }

    /**
     * @param string $unit
     * @return FilterParameterInterface
     */
    public function setUnit(string $unit): FilterParameterInterface
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @return string
     */
    public function getInputNameFrom(): string
    {
        return "{$this->getId()}[from]";
    }

    /**
     * @return string
     */
    public function getInputNameTo(): string
    {
        return "{$this->getId()}[to]";
    }

    /**
     * @return int|mixed
     */
    public function getValueFrom()
    {
        if ($selections = $this->getSelections()) {
            return isset($selections['from']) ? $selections['from'] : min($selections);
        }

        return $this->getMinValue();
    }

    /**
     * @return int|mixed
     */
    public function getValueTo()
    {
        if ($selections = $this->getSelections()) {
            return isset($selections['to']) ? $selections['to'] : max($selections);
        }

        return $this->getMaxValue();
    }
}
