<?php
namespace kr0lik\listFilter\interfaces;

interface FilterParameterValueInterface
{
    public function getValue();
    public function getName();
    public function getId(): string;
    public function isSelected(): bool;

    public function getParameterPageUrl(): string;
    public function getParameterPageTitle(): string;
}
