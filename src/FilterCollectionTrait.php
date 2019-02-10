<?php
namespace kr0lik\listFilter;

use Yii;
use yii\base\ErrorException;

trait FilterCollectionTrait
{
    private $parameters = [];

    /**
     * Add parameter to filter
     *
     * $name - Name of parameter in query
     * $type - Type of parameter. DEFAULT checkbox.
     *
     * @param string $name
     * @param string $type
     * @return FilterParameterInterface
     * @throws ErrorException
     */
    public function add(string $name, string $type = Filter::PARAMETER_DEFAULT): FilterParameterInterface
    {
        if (! $name) throw new ErrorException('Name cant be empty');
        if (isset($this->parameters[$name])) throw new ErrorException('Parameter {$parameterName} allready exists');

        $parameter = $this->makeParameter($name, $type);

        $this->parameters[$name] = $parameter;

        $this->prepared = false;

        return $parameter;
    }

    /**
     * Get all FilterParameter
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get parameter by name
     *
     * @param $name
     * @return FilterParameter|null
     */
    public function getParameter($name)
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Delete parameter from filter
     *
     * @param string $name
     * @return Filter
     */
    public function deleteParameter(string $name): self
    {
        unset($this->parameters[$name]);

        return $this;
    }

    private function makeParameter(string $name, string $type = Filter::PARAMETER_DEFAULT): FilterParameterInterface
    {
        switch($type) {
            case Filter::PARAMETER_DEFAULT:
                return new FilterParameterBase($name);
                break;
            case Filter::PARAMETER_BOOLEAN:
                return new FilterParameterBoolean($name);
                break;
            case Filter::PARAMETER_RANGE:
                return new FilterParameterRange($name);
                break;
            case Filter::PARAMETER_COLLECTION:
                return new FilterParameterCollection($name);
                break;
            default:
                throw new ErrorException("Unknown parameter type - '{$type}'");
        }
    }
}
