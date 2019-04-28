<?php
namespace kr0lik\listFilter\lib;

use kr0lik\listFilter\interfaces\FilterParameterInterface;

class FilterParameterFabric
{
    public static function create(string $type, string $id): FilterParameterInterface
    {
        $class = "kr0lik\listFilter\models\FilterParameter$type";

        if (class_exists($class)) {
            return new $class($id);
        }

        throw new ErrorException("Unknown parameter type - '{$type}'");
    }
}