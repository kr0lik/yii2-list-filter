<?php
namespace kr0lik\listFilter;

class FilterParameterCollectionAdapter implements FilterParameterInterface
{
    private $parameter;
    private $collection;

    protected $type;

    public function __construct(FilterParameterInterface $parameter, FilterParameterCollection $collection)
    {
        $this->parameter = $parameter;
        $this->collection = $collection;
    }

    public function __get($property)
    {
        if (isset($this->parameter->$property)) {
            return $this->parameter->$property;
        }
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

    public function getType(): string
    {
        return $this->parameter->getType();
    }

    public function getName(): string
    {
        return $this->parameter->getName();
    }

    public function setTitle(string $title): FilterParameterInterface
    {
        return $this->parameter->setTitle($title);
    }

    public function getTitle(): string
    {
        return $this->parameter->getTitle();
    }

    public function addValue($key, $value, string $url = null, string $title = null): FilterParameterInterface
    {
        return $this->parameter->addValue($key, $value, $url, $title);
    }

    public function getValues(): array
    {
        return $this->parameter->getValues();
    }

    public function hasValues(): bool
    {
        return $this->parameter->hasValues();
    }

    public function addSelect($select): FilterParameterInterface
    {
        return $this->parameter->addSelect($select);
    }

    public function getSelections(): array
    {
        return $this->parameter->getSelections();
    }

    public function hasSelections(): bool
    {
        return $this->parameter->hasSelections();
    }

    public function isSelected($key): bool
    {
        return $this->parameter->isSelected($key);
    }

    public function getSelectedValues(): array
    {
        return $this->parameter->getSelectedValues();
    }

    public function getInputName(): string
    {
        return $this->makeInputName($this->parameter->getInputName());
    }

    public function setScope($scope): FilterParameterInterface
    {
        return $this->parameter->setScope($scope);
    }

    public function getScope()
    {
        return $this->parameter->getScope();
    }

    public function prepare(): void
    {
        $this->parameter->prepare();
    }

    protected function makeInputName(string $name): string
    {
        $name = preg_replace('/^(\w+)\[(.+)/', '[${1}][${2}', $name);

        $collectionName = $this->collection->getInputName();
        $collectionName = str_replace('[]', '', $collectionName);

        return  "{$collectionName}{$name}";
    }
}