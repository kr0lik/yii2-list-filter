<?php
namespace kr0lik\listFilter;

class FilterParameterBoolean extends FilterParameterBase
{
    protected $type = Filter::PARAMETER_BOOLEAN;

    public function addValue($key, $value, string $url = null, string $title = null): FilterParameterInterface
    {
        $this->values = [];

        return parent::addValue($key, $value, $url, $title);
    }

    public function addSelect($select): FilterParameterInterface
    {
        $select = is_array($select) ? current($select) : $select;

        if ($select) {
            $this->selections = [$select];
        } else {
            $this->selections = [];
        }

        return $this;
    }

    public function getSelections(): array
    {
        $selections = [];

        if ($this->selections) {
            $select = current($this->selections);

            if (filter_var($select, FILTER_VALIDATE_BOOLEAN)) {
                $selections = [$select];
            }
        }

        return $selections;
    }

    public function isSelected($key = null): bool
    {
        if ($key === null) {
            return $this->hasSelections();
        }

        if ($this->getSelections()) {
            $select = current($this->getSelections());

            return $key == $select;
        }

        return false;
    }

    public function getInputName(): string
    {
        return $this->name;
    }
}