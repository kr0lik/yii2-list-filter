<?php
namespace kr0lik\listFilter;

use Yii;
use yii\base\ErrorException;

class FilterParameterCollection extends FilterParameterBase
{
    use FilterCollectionTrait;

    protected $type = Filter::PARAMETER_COLLECTION;

    public function add(string $name, string $type = Filter::PARAMETER_DEFAULT): FilterParameterInterface
    {
        if (! $name) throw new ErrorException('Name cant be empty');
        if (isset($this->parameters[$name])) throw new ErrorException('Parameter {$parameterName} allready exists');

        $parameter = $this->makeParameter($name, $type);

        $adapter = new FilterParameterCollectionAdapter($parameter, $this);

        $this->parameters[$name] = $adapter;

        $this->prepared = false;

        return $parameter;
    }

    public function addValue($key, $value, string $url = null, string $title = null): FilterParameterInterface
    {
        throw new ErrorException('This is collection parameter! Use $parameterCollection->getParameter($name)->addValue($key, $value, $url, $title)');
    }

    public function addSelect($select): FilterParameterInterface
    {
        throw new ErrorException('This is collection parameter! Use $parameterCollection->getParameter($name)->addSelect($select)');
    }

    public function getValues(): array
    {
        $values = $this->values;

        foreach ($this->getParameters() as $parameter) {
            $values = array_merge($values, $parameter->getValues());
        }

        return $values;
    }

    public function getSelections(): array
    {
        $selections = $this->selections;

        foreach ($this->getParameters() as $parameter) {
            $selections = array_unique(array_merge($selections, $parameter->getSelections()));
        }

        return $selections;
    }

    public function prepare(): void
    {
        foreach ($this->parameters as $parameter) {
            $parameter->prepare();
        }
    }
}