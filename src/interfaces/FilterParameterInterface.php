<?php
namespace kr0lik\listFilter\interfaces;

/**
 * Interface FilterParameterInterface
 * @package kr0lik\listFilter\interfaces
 */
interface FilterParameterInterface
{
    /**
     * @return string
     */
    public static function getType(): string;

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $title
     * @return FilterParameterInterface
     */
    public function setTitle(string $title): FilterParameterInterface;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string|callable $scope
     * @return FilterParameterInterface
     */
    public function setScope($scope): FilterParameterInterface;

    /**
     * @return mixed
     */
    public function getScope();

    /**
     * @param string|int|float $value
     * @param string|int|float|null $name
     * @param string|null $parameterPageUrl
     * @param string|null $parameterPageTitle
     * @return FilterParameterInterface
     */
    public function addValue($value, $name = null, ?string $parameterPageUrl = null, ?string $parameterPageTitle = null): FilterParameterInterface;

    /**
     * @return array
     */
    public function getValues(): array;

    /**
     * @param mixed $select
     * @return FilterParameterInterface
     */
    public function addSelect($select): FilterParameterInterface;

    /**
     * @return array
     */
    public function getSelections(): array;

    /**
     * @return array
     */
    public function getSelectedValues(): array;

    /**
     * @param string|int|float $value
     * @return bool
     */
    public function isSelected($value): bool;

    /**
     * @return string
     */
    public function getInputName(): string;
}
