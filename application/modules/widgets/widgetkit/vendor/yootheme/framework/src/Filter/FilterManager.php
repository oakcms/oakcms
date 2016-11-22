<?php

namespace YOOtheme\Framework\Filter;

class FilterManager
{
    /**
     * @var FilterInterface[]
     */
    protected $filters = array();

    /**
     * Applies a filter to the given value.
     *
     * @param  mixed  $value
     * @param  string $name
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function apply($value, $name)
    {
        if (!array_key_exists($name, $this->filters)) {
            throw new \InvalidArgumentException(sprintf('Filter "%s" is not defined.', $name));
        }

        if (is_string($class = $this->filters[$name])) {
            $this->filters[$name] = new $class;
        }

        $filter = clone $this->filters[$name];

        return $filter->filter($value);
    }

    /**
     * Registers a filter.
     *
     * @param string                 $name
     * @param string|FilterInterface $filter
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function register($name, $filter)
    {
        if (is_string($filter) && !class_exists($filter)) {
            throw new \InvalidArgumentException(sprintf('Unknown filter with the class name "%s".', $filter));
        }

        $this->filters[$name] = $filter;

        return $this;
    }
}
