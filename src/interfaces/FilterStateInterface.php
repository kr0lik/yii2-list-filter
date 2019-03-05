<?php
namespace kr0lik\listFilter\interfaces;

interface FilterStateInterface
{
    /**
     * Has come any parameter from query
     *
     * @return bool
     */
    public function hasFiltered(): bool;

    /**
     * Has any values in any parameter
     *
     * @return bool
     */
    public function hasValues(): bool;

    /**
     * Has any selected values in any parameter
     *
     * @return bool
     */
    public function hasSelections(): bool;
}