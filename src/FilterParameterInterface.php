<?php
namespace kr0lik\listFilter;

interface FilterParameterInterface
{
    public function getType(): string;

    public function getName(): string;
    public function setTitle(string $title): FilterParameterInterface;
    public function getTitle(): string;

    public function setScope($scope): FilterParameterInterface;
    public function getScope();

    public function addValue($key, $value, string $url = null, string $title = null): FilterParameterInterface;
    public function getValues(): array;
    public function hasValues(): bool;

    public function addSelect($select): FilterParameterInterface;
    public function getSelections(): array;
    public function hasSelections(): bool;

    public function isSelected($key): bool;

    public function getSelectedValues(): array;

    public function getInputName(): string;

    public function prepare(): void;
}