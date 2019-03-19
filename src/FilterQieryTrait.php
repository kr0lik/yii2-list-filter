<?php
namespace kr0lik\listFilter;

use yii\base\ErrorException;
use kr0lik\listFilter\interfaces\FilterCollectionInterface;

trait FilterQieryTrait
{
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
                    $query->andWhere([$parameter->getName() => $select]);
                }
            }
        }

        return $query;
    }
}
