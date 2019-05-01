<?php
namespace kr0lik\listFilter\interfaces;

/**
 * Interface FilterParameterValueInterface
 * @package kr0lik\listFilter\interfaces
 */
interface FilterParameterValueInterface
{
    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return bool
     */
    public function isSelected(): bool;

    /**
     * @return string
     */
    public function getParameterPageUrl(): string;

    /**
     * @return string
     */
    public function getParameterPageTitle(): string;
}
