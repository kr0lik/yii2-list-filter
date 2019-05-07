<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\FilterParameterTypeEnum;
use kr0lik\listFilter\interfaces\FilterParameterInterface;
use kr0lik\listFilter\lib\FilterParameterAbstract;

/**
 * Class FilterParameterCheckbox
 * @package kr0lik\listFilter\models
 */
class FilterParameterCheckbox extends FilterParameterAbstract implements FilterParameterInterface
{
    /**
     * @return string
     */
    public static function getType(): string
    {
        return FilterParameterTypeEnum::PARAMETER_CHECKBOX;
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
}
