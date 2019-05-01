<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\interfaces\{FilterParameterInterface,FilterParameterValueInterface};

/**
 * Class FilterParameterValue
 * @package kr0lik\listFilter\models
 */
class FilterParameterValue implements FilterParameterValueInterface
{
    /**
     * @var FilterParameterInterface
     */
    private $parameter;

    /**
     * @var string|int|float
     */
    private $value;
    /**
     * @var string|int|float|null
     */
    private $name;

    /**
     * @var string|null
     */
    protected $parameterPageUrl;
    /**
     * @var string|null
     */
    protected $parameterPageTitle;

    /**
     * FilterParameterValue constructor.
     * @param FilterParameterInterface $parameter
     * @param string|int|float $value
     * @param string|int|float|null $name
     * @param string|null $parameterPageUrl
     * @param string|null $parameterPageTitle
     */
    public function __construct(FilterParameterInterface $parameter, $value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null)
    {
        $this->parameter = $parameter;
        $this->value = is_string($value) ? trim($value): $value;
        $this->name = is_string($name) ? trim($name) : $name;

        $this->parameterPageUrl = $parameterPageUrl ? trim($parameterPageUrl) : null;
        $this->parameterPageTitle = $parameterPageTitle ? trim($parameterPageTitle) : null;
    }

    /**
     * @return mixed|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->name ?: $this->value;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->parameter->isSelected($this->getValue());
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return "{$this->parameter->getId()}--{$this->getValue()}";
    }

    /**
     * @return string
     */
    public function getParameterPageUrl(): string
    {
        return $this->parameterPageUrl ?: "?{$this->parameter->getInputName()}={$this->value}";
    }

    /**
     * @return string
     */
    public function getParameterPageTitle(): string
    {
        return $this->parameterPageTitle ?: (string) $this->value;
    }
}
