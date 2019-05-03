<?php
namespace kr0lik\listFilter;

use yii\base\ErrorException;
use kr0lik\listFilter\interfaces\FilterCollectionInterface;

/**
 * Trait FilterQieryTrait
 * @package kr0lik\listFilter
 */
trait FilterQieryTrait
{
    /**
     * @param FilterCollectionInterface $filter
     * @return ActiveRecord
     * @throws ErrorException
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
                        throw new ErrorException("Bad scope in parameter {$parameter->getId()}");
                    }
                } else {
                    $query->andWhere([$parameter->getId() => $select]);
                }
            }
        }

        return $query;
    }
}
