<?php
namespace kr0lik\filter\models;

use Yii;
use yii\db\ActiveQuery;
use kr0lik\filter\interfaces\{FilterCollectionInterface, FilterStateInterface};
use kr0lik\filter\lib\FilterCollectionTrait;

class Filter implements FilterCollectionInterface, FilterStateInterface
{
    const PARAMETER_CHECKBOX = 'checkbox';
    const PARAMETER_BOOLEAN = 'boolean';
    const PARAMETER_RANGE = 'range';
    const PARAMETER_COLLECTION = 'collection';

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
        $this->id = static::$autoIdPrefix . static::$counter++;;
    }

    public function hasFiltered(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasFiltered()) return true;
        }

        return false;
    }

    public function hasValues(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasValues()) return true;
        }

        return false;
    }

    public function hasSelections(): bool
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->hasSelections()) return true;
        }

        return false;
    }

    /**
     * Get selected values in any parameter
     * Values that is in selections
     *
     * @return array
     */
    public function getSelectedValues(): array
    {
        $selected = [];

        foreach ($this->getParameters() as $parameter) {
            $selected = array_merge($selected, $parameter->getSelectedValues());
        }

        return $selected;
    }
}
