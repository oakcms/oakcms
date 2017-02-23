<?php

namespace YOOtheme\Framework\Filter;

interface FilterInterface
{
    /**
     * Filter and return a value
     *
     * @param  mixed $value The value that should be filtered
     * @return mixed The filtered value
     */
    public function filter($value);
}