<?php
namespace kr0lik\listFilter;

class FilterParameterRange extends FilterParameterBase
{
    public $min = 0;
    public $max = 0;
    public $unit;

    private $step;

    protected $type = Filter::PARAMETER_RANGE;

    public function addValue($key, $value, string $url = null, string $title = null): FilterParameterInterface
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

        return parent::addValue($key, $value, $url, $title);
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

    public function setUnit(string $unit): FilterParameterInterface
    {
        $this->unit = $unit;

        return $this;
    }

    public function hasValues(): bool
    {
        if ($this->min !== null || $this->max !== null) {
            return true;
        }

        return parent::hasValues();
    }

    public function getInputName(): string
    {
        return $this->name;
    }

    public function getInputNameFrom(): string
    {
        return "{$this->getInputName()}[from]";
    }

    public function getInputNameTo(): string
    {
        return "{$this->getInputName()}[to]";
    }

    public function getValueFrom()
    {
        $selections = $this->getSelections();

        return isset($selections['from']) ?: $this->min;
    }

    public function getValueTo()
    {
        $selections = $this->getSelections();

        return isset($selections['to']) ?: $this->max;
    }
}