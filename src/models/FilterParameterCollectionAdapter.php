<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\lib\FilterParameterAbstract;
use kr0lik\listFilter\interfaces\FilterParameterInterface;

/**
 * Class FilterParameterCollectionAdapter
 * @package kr0lik\listFilter\models
 */
class FilterParameterCollectionAdapter extends FilterParameterAbstract
{
    /**
     * @var FilterParameterInterface
     */
    private $parameter;
    /**
     * @var FilterParameterCollection
     */
    private $collection;

    /**
     * FilterParameterCollectionAdapter constructor.
     * @param FilterParameterInterface $parameter
     * @param FilterParameterCollection $collection
     */
    public function __construct(FilterParameterInterface $parameter, FilterParameterCollection $collection)
    {
        $this->parameter = $parameter;
        $this->collection = $collection;
		
		parent::__construct("adapter_{$parameter->getId()}");
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return string
     */
    public function __call($method, $parameters)
    {
        if (strpos($method,'getInputName') !== false) {
            return $this->makeInputName(  $this->parameter->$method($parameters) );
        }

        if (method_exists($this->parameter, $method)) {
            return $this->parameter->$method($parameters);
        }
    }

    // Override methods

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->parameter->getType();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->parameter->getId();
    }

    /**
     * @param string $title
     * @return FilterParameterInterface
     */
    public function setTitle(string $title): FilterParameterInterface
    {
        $this->parameter->setTitle($title);

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->parameter->getTitle();
    }

    /**
     * @param string $scope
     * @return FilterParameterInterface
     */
    public function setScope($scope): FilterParameterInterface
    {
        $this->parameter->setScope($scope);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->parameter->getScope();
    }

    /**
     * @param mixed $select
     * @return FilterParameterInterface
     */
    public function addSelect($select): FilterParameterInterface
    {
        $this->parameter->addSelect($select);

        return $this;
    }

    /**
     * @return array
     */
    public function getSelections(): array
    {
        return $this->parameter->getSelections();
    }

    /**
     * @param mixed $value
     * @param null $name
     * @param string|null $parameterPageUrl
     * @param string|null $parameterPageTitle
     * @return FilterParameterInterface
     */
    public function addValue($value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null): FilterParameterInterface
    {
        $this->parameter->addValue($value, $name, $parameterPageUrl, $parameterPageTitle);

        return $this;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        $values = $this->parameter->getValues();
        $newValues = [];

        foreach ($values as $value) {
            $newValues[] = $this->makeValue($value->getValue(), $value->getName(), $value->getParameterPageUrl(), $value->getParameterPageTitle());
        }

        unset($values);

        return $newValues;
    }

    /**
     * @return array
     */
    public function getSelectedValues(): array
    {
        $values = [];

        foreach ($this->getValues() as $value) {
            if (in_array($value->getValue(), $this->parameter->getSelections())) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * @return bool
     */
    public function hasFiltered(): bool
    {
        return $this->collection->hasFiltered();
    }

    /**
     * @return string
     */
    public function getInputName(): string
    {
        return $this->makeInputName($this->parameter->getInputName());
    }

    /**
     * @param string $name
     * @return string
     */
    protected function makeInputName(string $name): string
    {
        $name = preg_replace('/^(\w+)\[(.+)/', '[${1}][${2}', $name);

        $collectionName = $this->collection->getInputName();
        $collectionName = str_replace('[]', '', $collectionName);

        return  "{$collectionName}{$name}";
    }
}
