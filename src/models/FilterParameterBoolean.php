<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\interfaces\FilterParameterInterface;
use kr0lik\listFilter\lib\FilterParameterAbstract;

/**
 * Class FilterParameterBoolean
 * @package kr0lik\listFilter\models
 */
class FilterParameterBoolean extends FilterParameterAbstract
{
    /**
     * @param mixed $value
     * @param null $name
     * @param string|null $parameterPageUrl
     * @param string|null $parameterPageTitle
     * @return FilterParameterInterface
     */
    public function addValue($value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null): FilterParameterInterface
    {
        $this->values = [];

        return parent::addValue($value, $name, $parameterPageUrl, $parameterPageTitle);
    }

    /**
     * @param mixed $select
     * @return FilterParameterInterface
     */
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

    /**
     * @return string
     */
    public function getInputName(): string
    {
        return $this->getId();
    }
}
