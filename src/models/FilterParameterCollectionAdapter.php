<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\lib\FilterParameterAbstract;
use kr0lik\listFilter\interfaces\{FilterParameterInterface, FilterCollectionInterface};

class FilterParameterCollectionAdapter extends FilterParameterAbstract
{
    private $parameter;
    private $collection;

    public function __construct(FilterParameterInterface $parameter, FilterCollectionInterface $collection)
    {
        $this->parameter = $parameter;
        $this->collection = $collection;
    }

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
    public function getType(): ?string
    {
        return $this->parameter->getType();
    }

    public function getId(): string
    {
        return $this->parameter->getId();
    }

    public function setTitle(string $title): FilterParameterInterface
    {
        $this->parameter->setTitle($title);

        return $this;
    }

    public function getTitle(): string
    {
        return $this->parameter->getTitle();
    }

    public function setScope($scope): FilterParameterInterface
    {
        $this->parameter->setScope($scope);

        return $this;
    }

    public function getScope()
    {
        return $this->parameter->getScope();
    }

    public function addSelect($select): FilterParameterInterface
    {
        $this->parameter->addSelect($select);

        return $this;
    }

    public function getSelections(): array
    {
        return $this->parameter->getSelections();
    }

    public function addValue($value, $name, string $parameterPageUrl = '', string $parameterPageTitle = ''): FilterParameterInterface
    {
        $this->parameter->addValue($value, $name, $parameterPageUrl, $parameterPageTitle);

        return $this;
    }

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

    public function hasFiltered(): bool
    {
        return $this->collection->hasFiltered();
    }

    public function getInputName(): string
    {
        return $this->makeInputName($this->parameter->getInputName());
    }

    protected function makeInputName(string $name): string
    {
        $name = preg_replace('/^(\w+)\[(.+)/', '[${1}][${2}', $name);

        $collectionName = $this->collection->getInputName();
        $collectionName = str_replace('[]', '', $collectionName);

        return  "{$collectionName}{$name}";
    }
}
