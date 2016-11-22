<?php

namespace YOOtheme\Framework\Routing;

class Route
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var mixed
     */
    protected $callable;

    /**
     * @var array
     */
    protected $methods = array();

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Constructor.
     *
     * @param string $pattern
     * @param mixed  $callable
     */
    public function __construct($pattern, $callable)
    {
        $this->pattern  = $pattern;
        $this->callable = $callable;
    }

    /**
     * Gets the route pattern.
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Gets the route callable.
     *
     * @return mixed
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Gets the supported HTTP methods.
     *
     * @return self
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Sets the supported HTTP methods.
     *
     * @param  string|string[] $method
     * @return self
     */
    public function setMethods($method)
    {
        $this->methods = array_merge($this->methods, (array) $method);

        return $this;
    }

    /**
     * Gets the route parameters.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Returns the options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the options.
     *
     * @param  array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Matches the given URL?
     *
     * @param  string $url
     * @return bool
     */
    public function matches($url)
    {
        $regex = '#^' . preg_replace_callback('#:([\w]+)#', array($this, 'matchesCallback'), str_replace(')', ')?', $this->pattern)) . '$#';

        if (!preg_match($regex, $url, $values)) {
            return false;
        }

        foreach ($this->params as $name => $null) {
            if (isset($values[$name])) {
                $this->params[$name] = urldecode($values[$name]);
            }
        }

        return true;
    }

    /**
     * Convert a URL parameter to regex.
     *
     * @param  array $matches
     * @return string
     */
    protected function matchesCallback($matches)
    {
        $this->params[$matches[1]] = null;

        return '(?P<' . $matches[1] . '>[^/]+)';
    }
}
