<?php
namespace kr0lik\listFilter;

use Yii;
use yii\db\ActiveQuery;
use kr0lik\listFilter\interfaces\{FilterCollectionInterface, FilterStateInterface};
use kr0lik\listFilter\lib\FilterCollectionTrait;

class Filter implements FilterCollectionInterface, FilterStateInterface
{
    const PARAMETER_CHECKBOX = 'Checkbox';
    const PARAMETER_BOOLEAN = 'Boolean';
    const PARAMETER_RANGE = 'Range';
    const PARAMETER_COLLECTION = 'Collection';

    /**
     * Filter id
     *
     * @var string
     */
    public $id;

    private static $autoIdPrefix = 'f';
    private static $counter = 0;

    use FilterCollectionTrait;

    public function __construct()
    {
        $this->id = static::$autoIdPrefix . static::$counter++;
    }
}
