<?php
namespace kr0lik\listFilter\interfaces;

interface FilterCollectionInterface
{
    /**
     * Add new parameter
     *
     * $id - Name of parameter in query string
     * $type - Type of parameter.
     *
     * @param string $name
     * @param string $type
     * @return FilterParameterInterface
     */
    public function add(string $id, string $type): FilterParameterInterface;

    /**
     * Get all FilterParameter
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Get parameter by name
     *
     * @param $name
     * @return FilterParameterInterface|null
     */
    public function getParameter($id): ?FilterParameterInterface;

    /**
     * Delete parameter from filter
     *
     * @param string $name
     * @return FilterInterface
     */
    public function deleteParameter(string $id): FilterCollectionInterface;

    /**
     * Get selected values in any parameter
     * Values that is in selections
     *
     * @return array
     */
    public function getSelectedValues(): array;
}