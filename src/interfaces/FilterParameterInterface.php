<?php
namespace kr0lik\listFilter\interfaces;

interface FilterParameterInterface
{
    public function getType(): ?string;
    public function getid(): string;

    public function setTitle(string $title): FilterParameterInterface;
    public function getTitle(): string;

    public function setScope($scope): FilterParameterInterface;
    public function getScope();

    public function addValue($value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null): FilterParameterInterface;
    public function getValues(): array;

    public function getSelections(): array;
    public function getSelectedValues(): array;
    public function isSelected($value): bool;

    public function getInputName(): string;
}
