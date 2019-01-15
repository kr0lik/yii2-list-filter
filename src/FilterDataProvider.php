<?php
namespace kr0lik\listFilter;

use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

class FilterDataProvider extends ActiveDataProvider
{
    public $filter;

    public function init()
    {
        parent::init();

        $this->prepareFilter();
    }

    protected function prepareFilter()
    {
        if (! $this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }

        if (! $this->filter instanceof Filter) {
            throw new InvalidConfigException('The "filter" property must be an instance of the "' . Filter::class . '" class.');
        }

        $this->filter->prepare();
        foreach ($this->filter->getParameters() as $parameter) {
            if ($select = $parameter->getSelections()) {

                if (is_string($parameter->scope)) {
                    $this->query->{$parameter->scope}($select);
                } elseif (is_callable($parameter->scope)) {
                    ($parameter->scope)($this->query, $select);
                } else {
                    throw new InvalidConfigException("Bad scope in {$parameter->title}");
                }
            }
        }
    }
}
