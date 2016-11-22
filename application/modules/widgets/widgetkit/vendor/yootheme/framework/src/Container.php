<?php

namespace YOOtheme\Framework;

class Container implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $values = array();

    /**
     * @var array
     */
    protected $raw = array();

    /**
     * @var array
     */
    protected $factories = array();

    /**
     * @var array
     */
    protected static $containers = array();

    /**
     * Constructor.
     *
     * @param array $values
     *
     * @throws \RuntimeException
     */
    public function __construct(array $values = array())
    {
        $class = get_called_class();

        foreach ($values as $name => $value) {
            $this->offsetSet($name, $value);
        }

        if (isset(static::$containers[$class])) {
            throw new \RuntimeException(sprintf('Container for class "%s" already exists.', $class));
        }

        static::$containers[$class] = $this;
    }

    /**
     * Gets a container instance.
     *
     * @param  array $values
     * @return Container
     */
    public static function getInstance(array $values = array())
    {
        $class = get_called_class();

        if (!isset(static::$containers[$class])) {
            static::$containers[$class] = new static($values);
        }

        return static::$containers[$class];
    }

    /**
     * Checks if a parameter or service is defined.
     *
     * @param  string $name
     * @return bool
     */
    public static function has($name)
    {
        return static::getInstance()->offsetExists($name);
    }

    /**
     * Gets a parameter or service.
     *
     * @param  string $name
     * @return mixed
     */
    public static function get($name)
    {
        return static::getInstance()->offsetGet($name);
    }

    /**
     * Sets a parameter or service.
     *
     * @param string $name
     * @param mixed  $value
     */
    public static function set($name, $value)
    {
        static::getInstance()->offsetSet($name, $value);
    }

    /**
     * Removes a parameter or service.
     *
     * @param string $name
     */
    public static function remove($name)
    {
        static::getInstance()->offsetUnset($name);
    }

    /**
     * Resets all parameters and services.
     */
    public static function reset()
    {
        $container            = static::getInstance();
        $container->values    = array();
        $container->factories = array();
        $container->raw       = array();
    }

    /**
     * Sets a closure as a factory service.
     *
     * @param string   $name
     * @param \Closure $closure
     */
    public static function factory($name, \Closure $closure)
    {
        $container = static::getInstance();
        $container->offsetSet($name, $closure);
        $container->factories[$name] = true;
    }

    /**
     * Extends an existing service definition.
     *
     * @param string   $name
     * @param \Closure $closure
     *
     * @throws \InvalidArgumentException
     */
    public static function extend($name, \Closure $closure)
    {
        $container = static::getInstance();

        if (!array_key_exists($name, $container->values)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not defined.', $name));
        }

        if (!($container->values[$name] instanceof \Closure)) {
            throw new \InvalidArgumentException(sprintf('"%s" service definition is not a Closure.', $name));
        }

        $factory = $container->values[$name];

        $container->offsetSet($name, function ($c) use ($closure, $factory) {
            return $closure($factory($c), $c);
        });
    }

    /**
     * Gets a parameter or service without resolving.
     *
     * @param  string $name
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function raw($name)
    {
        $container = static::getInstance();

        if (!array_key_exists($name, $container->values)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not defined.', $name));
        }

        return isset($container->raw[$name]) ? $container->raw[$name] : $container->values[$name];
    }

    /**
     * Returns all defined names.
     *
     * @return array
     */
    public static function keys()
    {
        $container = static::getInstance();

        return array_keys($container->values);
    }

    /**
     * Magic method to access the container in a static context.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        $container = static::getInstance();

        return $args ? call_user_func_array($container[$name], $args) : $container[$name];
    }

    /**
     * Checks if a parameter or service is defined.
     *
     * @param  string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return array_key_exists($name, $this->values);
    }

    /**
     * Gets a parameter or service.
     *
     * @param  string $name
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function offsetGet($name)
    {
        if (!array_key_exists($name, $this->values)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not defined.', $name));
        }

        if (array_key_exists($name, $this->raw) || !($this->values[$name] instanceof \Closure)) {
            return $this->values[$name];
        }

        if (isset($this->factories[$name])) {
            return $this->values[$name]($this);
        }

        $this->raw[$name] = $this->values[$name];

        return $this->values[$name] = $this->values[$name]($this);
    }

    /**
     * Sets a parameter or service.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws \RuntimeException
     */
    public function offsetSet($name, $value)
    {
        if (array_key_exists($name, $this->raw)) {
            throw new \RuntimeException(sprintf('Cannot override service definition "%s".', $name));
        }

        $this->values[$name] = $value;
    }

    /**
     * Removes a parameter or service.
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        if (array_key_exists($name, $this->values)) {
            unset($this->values[$name], $this->raw[$name], $this->factories[$name]);
        }
    }
}
