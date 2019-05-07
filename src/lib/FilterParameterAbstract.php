<?php
namespace kr0lik\listFilter\lib;

use Yii;
use kr0lik\listFilter\interfaces\{FilterParameterInterface,FilterParameterValueInterface,FilterStateInterface};
use kr0lik\listFilter\models\FilterParameterValue;

/**
 * Class FilterParameterAbstract
 * @package kr0lik\listFilter\lib
 */
abstract class FilterParameterAbstract implements FilterParameterInterface, FilterStateInterface
{
    /**
     * @var string
     */
    protected $id = '';
    /**
     * @var string
     */
    protected $title = '';
    /**
     * @var mixed
     */
    protected $scope;

    /**
     * @var array
     */
    protected $values = [];
    /**
     * @var array
     */
    protected $selections = [];

    /**
     * @var bool
     */
    protected $hasFiltered = false;

    /**
     * FilterParameterAbstract constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    abstract public static function getType(): string;

    /**
     * name of parameter in query string
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Title for parameter
     *
     * @param string $title
     * @return FilterParameterInterface
     */
    public function setTitle(string $title): FilterParameterInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Can be name of your method in query class for model
     * Example 'byId' equal in your query class: public function byId($select) { return $this->andWhere(['id' => $select]); }
     * Or anonymous function:
     * Example: function ($query, $select) { return $query->andWhere(['id' => $select]) }
     *
     * @param string $scope
     * @return FilterParameterInterface
     */
    public function setScope($scope): FilterParameterInterface
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * For use in query class for your model
     *
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     *  Name for input field
     *
     * @return string
     */
    public function getInputName(): string
    {
        return "{$this->getId()}[]";
    }

    /**
     * Add new value for this parameter
     *
     * @param mixed $value
     * @param mixed $name
     * @param string $parameterPageUrl
     * @param string $parameterPageTitle
     * @return FilterParameterInterface
     */
    public function addValue($value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null): FilterParameterInterface
    {
        $this->values[] = $this->makeValue($value, $name, $parameterPageUrl, $parameterPageTitle);

        return $this;
    }

    /**
     * @return array [FilterParameterValueInterface]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Add key to selections
     *
     * @param mixed $select
     * @return FilterParameterInterface
     */
    abstract public function addSelect($select): FilterParameterInterface;


    /**
     * Get array keys that selected
     *
     * @return array
     */
    public function getSelections(): array
    {
        $this->prepare();

        return $this->selections;
    }

    /**
     * Array of values that was selected
     *
     * @return array [FilterParameterValueInterface]
     */
    public function getSelectedValues(): array
    {
        $this->prepare();

        $values = [];

        foreach ($this->getValues() as $value) {
            if (in_array($value->getValue(), $this->getSelections())) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * Check key is in selections
     *
     * @param string|int|float $value
     * @return bool
     */
    public function isSelected($value): bool
    {
        return in_array($value, $this->getSelections());
    }


    /**
     * @return bool
     */
    public function hasFiltered(): bool
    {
        $this->prepare();

        return $this->hasFiltered;
    }

    /**
     * @return bool
     */
    public function hasValues(): bool
    {
        return (bool) $this->getValues();
    }

    /**
     * @return bool
     */
    public function hasSelections(): bool
    {
        return (bool) $this->getSelections();
    }


    /**
     * @param string|int|float $value
     * @param string|int|float|null $name
     * @param string|null $parameterPageUrl
     * @param string|null $parameterPageTitle
     * @return FilterParameterValueInterface
     */
    protected function makeValue($value, $name = null, string $parameterPageUrl = null, string $parameterPageTitle = null): FilterParameterValueInterface
    {
        return new FilterParameterValue($this, $value, $name, $parameterPageUrl, $parameterPageTitle);
    }

    protected function prepare(): void
    {
        if (! $this->hasFiltered) {
            if (($select = Yii::$app->request->getQueryParam($this->getId())) != null) {
                $this->addSelect($select);
            }

            $this->hasFiltered = true;
        }
    }
}
