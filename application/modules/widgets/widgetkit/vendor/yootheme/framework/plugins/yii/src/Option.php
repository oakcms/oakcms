<?php

namespace YOOtheme\Framework\Yii;

class Option
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * Constructor.
     *
     * @param string $prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Gets a value.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return get_option($this->prefix.$name, $default);
    }

    /**
     * Sets a value.
     *
     * @param  string $name
     * @param  mixed $value
     * @return bool
     */
    public function set($name, $value)
    {
        return update_option($this->prefix.$name, $value);
    }
}
