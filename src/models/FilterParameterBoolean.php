<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\interfaces\FilterParameterInterface;
use kr0lik\listFilter\lib\FilterParameterAbstract;

class FilterParameterBoolean extends FilterParameterAbstract
{
    public function addValue($key, $name, string $parameterPageUrl = '', string $parameterPageTitle = ''): FilterParameterInterface
    {
        $this->values = [];

        return parent::addValue($key, $name, $parameterPageUrl, $parameterPageTitle);
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