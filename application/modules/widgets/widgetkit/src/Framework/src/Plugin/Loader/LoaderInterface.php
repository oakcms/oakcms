<?php

namespace YOOtheme\Widgetkit\Framework\Plugin\Loader;

interface LoaderInterface
{
    /**
     * Loads the plugin.
     *
     * @param  string $name
     * @param  array  $config
     * @return array
     */
    public function load($name, array $config);
}
