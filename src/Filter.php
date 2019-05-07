<?php
namespace kr0lik\listFilter;

use Yii;
use yii\db\ActiveQuery;
use kr0lik\listFilter\interfaces\{FilterCollectionInterface, FilterStateInterface};
use kr0lik\listFilter\lib\FilterCollectionTrait;

/**
 * Class Filter
 * @package kr0lik\listFilter
 */
class Filter implements FilterCollectionInterface
{
    /**
     * Filter id
     *
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    private static $autoIdPrefix = 'f';
    /**
     * @var int
     */
    private static $counter = 0;

    use FilterCollectionTrait;

    /**
     * Filter constructor.
     */
    public function __construct()
    {
        $this->id = static::$autoIdPrefix . static::$counter++;
    }
}
