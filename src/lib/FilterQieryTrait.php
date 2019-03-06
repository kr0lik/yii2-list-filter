<?php
namespace kr0lik\filter\lib;

use yii\base\ErrorException;
use kr0lik\filter\interfaces\FilterCollectionInterface;

trait FilterQieryTrait
{
    public function byFilter(FilterCollectionInterface $filter): ActiveQuery
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
                        throw new ErrorException("Bad scope in parameter {$parameter->getName()}");
                    }
                } else {
                    $query->andWhere([$parameter->getName() => $select]);
                }
            }
        }

        return $query;
    }
}