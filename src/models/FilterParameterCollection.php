<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\lib\FilterParameterAbstract;
use yii\base\ErrorException;
use kr0lik\listFilter\Filter;
use kr0lik\listFilter\interfaces\{FilterCollectionInterface, FilterParameterInterface, FilterStateInterface};
use kr0lik\listFilter\lib\{FilterCollectionTrait, FilterParameterFabric};

/**
 * Class FilterParameterCollection
 * @package kr0lik\listFilter\models
 */
class FilterParameterCollection extends FilterParameterAbstract implements FilterCollectionInterface
{
    use FilterCollectionTrait;

    /**
     * @param string $id
     * @param string $type
     * @return FilterParameterInterface
     * @throws ErrorException
     */
    public function add(string $id, string $type = Filter::PARAMETER_CHECKBOX): FilterParameterInterface
    {
        $this->validateParameterId($id);

        $parameter = FilterParameterFabric::create($type, $id);

        $adapter = new FilterParameterCollectionAdapter($parameter, $this);

        $this->parameters[$id] = $adapter;

        return $parameter;
    }

    // ToDo: Liskov substitution

    /**
     * @param mixed $value
     * @param null $name
     * @param string|null $parameterPageUrl
     * @param string|null $parameterPageTitle
     * @return FilterParameterInterface
     * @throws ErrorException
     */
    public function addValue($value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null): FilterParameterInterface
    {
        throw new ErrorException('You new get parameter form this collection and add value there');

        return $this;
    }

    // ToDo: Liskov substitution

    /**
     * @param mixed $select
     * @return FilterParameterInterface
     * @throws ErrorException
     */
    public function addSelect($select): FilterParameterInterface
    {
        throw new ErrorException('You new get parameter form this collection and add select there');

        return $this;
    }


    /**
     * @return array
     */
    public function getSelections(): array
    {
        $selections = [];

        foreach ($this->getParameters() as $parameter) {
            $selections = array_unique(array_merge($selections, $parameter->getSelections()));
        }

        return $selections;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        $values = [];

        foreach ($this->getParameters() as $parameter) {
            $values = array_merge($values, $parameter->getValues());
        }

        return $values;
    }
}
