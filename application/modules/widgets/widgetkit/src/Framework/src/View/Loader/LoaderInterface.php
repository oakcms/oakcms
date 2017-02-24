<?php

namespace YOOtheme\Widgetkit\Framework\View\Loader;

interface LoaderInterface
{
    /**
     * Loads a template.
     *
     * @param  string $name
     * @return string|false
     */
    public function load($name);
}
