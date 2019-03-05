<?php
namespace kr0lik\listFilter\models;

use kr0lik\listFilter\interfaces\{FilterParameterInterface,FilterParameterValueInterface};

class FilterParameterValue implements FilterParameterValueInterface
{
    private $parameter;

    private $key;
    private $value;

    protected $parameterPageUrl;
    protected $parameterPageTitle;

    public function __construct(FilterParameterInterface $parameter, $key, $value, string $parameterPageUrl = '', string $parameterPageTitle = '')
    {
        $this->parameter = $parameter;
        $this->key = trim($key);
        $this->value = trim($value);

        $this->parameterPageUrl = trim($parameterPageUrl);
        $this->parameterPageTitle = trim($parameterPageTitle);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isSelected(): bool
    {
        return $this->parameter->isSelected($this->getKey());
    }

    public function getId(): string
    {
        return "{$this->parameter->getId()}--{$this->getKey()}";
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