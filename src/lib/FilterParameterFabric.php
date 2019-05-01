<?php
namespace kr0lik\listFilter\lib;

use yii\base\ErrorException;
use kr0lik\listFilter\interfaces\FilterParameterInterface;

/**
 * Class FilterParameterFabric
 * @package kr0lik\listFilter\lib
 */
class FilterParameterFabric
{
    /**
     * @param string $type
     * @param string $id
     * @return FilterParameterInterface
     * @throws ErrorException
     */
    public static function create(string $type, string $id): FilterParameterInterface
    {
        $class = "kr0lik\listFilter\models\FilterParameter$type";

        if (! class_exists($class)) {
            throw new ErrorException("Unknown parameter type - '{$type}'");
        }

        return new $class($id);
    }
}