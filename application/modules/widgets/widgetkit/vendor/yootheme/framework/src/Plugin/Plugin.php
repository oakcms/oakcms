<?php

namespace YOOtheme\Framework\Plugin;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class Plugin extends ApplicationAware implements PluginInterface
{
    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }

        if (!isset($this->config)) {
            $this->config = array();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function main(Application $app)
    {
        if (is_callable($this->main)) {
            call_user_func($this->main, $app, $this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function config($key = null, $default = null)
    {
        if (null === $key) {
            return $this->config;
        }

        $array = $this->config;

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {

            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }
}
