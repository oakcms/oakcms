<?php

namespace YOOtheme\Framework\View\Asset\Filter;

class FilterManager
{
    /**
     * @var array
     */
    protected $filters = array();

    /**
     * Constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters = array())
    {
        foreach ($filters as $name => $filter) {
            $this->add($name, $filter);
        }
    }

    /**
     * Gets one or multiple filters.
     *
     * @param  string|array $name
     * @return FilterInterface|FilterInterface[]
     */
    public function get($name)
    {
        if (is_array($name)) {

            $filters = array_flip($name);

            foreach ($filters as $name => $i) {
                $filters[$name] = $this->filters[$name];
            }

            return $filters;
        }

        return isset($this->filters[$name]) ? $this->filters[$name] : null;
    }

    /**
     * Adds a named filter.
     *
     * @param  string $name
     * @param  mixed  $filter
     * @return self
     */
    public function add($name, $filter)
    {
        if (is_string($filter)) {
            $filter = new $filter;
        }

        $this->filters[$name] = $filter;

        return $this;
    }

    /**
     * Removes a named filter.
     *
     * @param  string $name
     * @return self
     */
    public function remove($name)
    {
        unset($this->filters[$name]);

        return $this;
    }
}
