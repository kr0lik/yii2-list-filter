<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\lib\FilterParameterAbstract;
use yii\base\ErrorException;
use kr0lik\listFilter\Filter;
use kr0lik\listFilter\interfaces\{FilterCollectionInterface, FilterParameterInterface, FilterStateInterface};
use kr0lik\listFilter\lib\FilterCollectionTrait;

class FilterParameterCollection extends FilterParameterAbstract implements FilterCollectionInterface
{
    use FilterCollectionTrait;

    public function add(string $id, string $type = Filter::PARAMETER_CHECKBOX): FilterParameterInterface
    {
        $this->validateParameterId($id);

        $parameter = $this->makeParameter($id, $type);

        $adapter = new FilterParameterCollectionAdapter($parameter, $this);

        $this->parameters[$id] = $adapter;

        return $parameter;
    }

    // ToDo: Liskov substitution
    public function addValue($value, $name, string $parameterPageUrl = '', string $parameterPageTitle = ''): FilterParameterInterface
    {
        throw new ErrorException('You neew get parameter form this collection and add value there');

        return $this;
    }

    // ToDo: Liskov substitution
    public function addSelect($select): FilterParameterInterface
    {
        throw new ErrorException('You neew get parameter form this collection and add select there');

        return $this;
    }


    public function getSelections(): array
    {
        $selections = [];

        foreach ($this->getParameters() as $parameter) {
            $selections = array_unique(array_merge($selections, $parameter->getSelections()));
        }

        return $selections;
    }

    public function getValues(): array
    {
        $values = [];

        foreach ($this->getParameters() as $parameter) {
            $values = array_merge($values, $parameter->getValues());
        }

        return $values;
    }
}
