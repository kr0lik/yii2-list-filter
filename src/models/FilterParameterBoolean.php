<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\interfaces\FilterParameterInterface;
use kr0lik\listFilter\lib\FilterParameterAbstract;

class FilterParameterBoolean extends FilterParameterAbstract
{
    public function addValue($value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null): FilterParameterInterface
    {
        $this->values = [];

        return parent::addValue($value, $name, $parameterPageUrl, $parameterPageTitle);
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

    public function getInputName(): string
    {
        return $this->getId();
    }
}
