<?php
namespace kr0lik\listFilter\lib;

use yii\base\ErrorException;
use kr0lik\listFilter\models\{FilterParameterBoolean, FilterParameterCheckbox, FilterParameterCollection, FilterParameterRange};
use kr0lik\listFilter\interfaces\FilterParameterInterface;

/**
 * Class FilterParameterFabric
 * @package kr0lik\listFilter\lib
 */
class FilterParameterFabric
{
    /**
     * @var array
     */
    private static $map = [
        FilterParameterCheckbox::class,
        FilterParameterBoolean::class,
        FilterParameterRange::class,
        FilterParameterCollection::class
    ];

    /**
     * @param string $type
     * @param string $id
     * @return FilterParameterInterface
     * @throws ErrorException
     */
    public static function create(string $type, string $id): FilterParameterInterface
    {
        foreach (self::$map as $class) {
            if ($class::getType() === $type) return new $class($id);
        }

        throw new ErrorException("Unknown parameter type - '{$type}'");
    }
}