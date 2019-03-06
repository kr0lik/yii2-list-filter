<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\interfaces\{FilterParameterInterface,FilterParameterValueInterface};

class FilterParameterValue implements FilterParameterValueInterface
{
    private $parameter;

    private $value;
    private $name;

    protected $parameterPageUrl;
    protected $parameterPageTitle;

    public function __construct(FilterParameterInterface $parameter, $value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null)
    {
        $this->parameter = $parameter;
        $this->value = trim($value);
        $this->name = trim($name);

        $this->parameterPageUrl = trim($parameterPageUrl);
        $this->parameterPageTitle = trim($parameterPageTitle);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getName()
    {
        return $this->name ?: $this->value;
    }

    public function isSelected(): bool
    {
        return $this->parameter->isSelected($this->getValue());
    }

    public function getId(): string
    {
        return "{$this->parameter->getId()}--{$this->getValue()}";
    }

    public function getParameterPageUrl(): string
    {
        return $this->parameterPageUrl ?: "?{$this->parameter->getInputName()}={$this->key}";
    }

    public function getParameterPageTitle(): string
    {
        return $this->parameterPageTitle ?: $this->value;
    }
}
