<?php
namespace kr0lik\listFilter;

use yii\db\ActiveRecord;
use kr0lik\listFilter\exception\ListFilterException;
use kr0lik\listFilter\interfaces\FilterCollectionInterface;

/**
 * Trait FilterQueryTrait
 * @package kr0lik\listFilter
 */
trait FilterQueryTrait
{
    /**
     * @param FilterCollectionInterface $filter
     * @throws ListFilterException
     * @return ActiveRecord
     */
    public function byFilter(FilterCollectionInterface $filter)
    {
        $query = $this;

        foreach ($filter->getParameters() as $parameter) {
            $select = $parameter->getSelections();

            if ($select !== []) {
                if ($scope = $parameter->getScope()){
                    if (is_string($scope)) {
                        $query->{$scope}($select);
                    } elseif (is_callable($scope)) {
                        ($scope)($query, $select);
                    } else {
                        throw new ListFilterException("Bad scope in parameter {$parameter->getId()}");
                    }
                } else {
                    $query->andWhere([$parameter->getId() => $select]);
                }
            }
        }

        return $query;
    }
}
