<?php

namespace YOOtheme\Framework\Routing;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class ControllerCollection
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $routes = array();

    /**
     * @var array
     */
    protected $controllers = array();

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Adds a controller.
     *
     * @param string|ControllerInterface $controller
     */
    public function add($controller)
    {
        if (is_string($controller) && class_exists($controller)) {
            $controller = new $controller;
        }

        if ($controller instanceof ApplicationAware) {
            $controller->setApplication($this->app);
        }

        $this->controllers[] = $controller;
    }

    /**
     * Maps a pattern to a callable.
     *
     * @param  string $pattern
     * @param  mixed  $callable
     * @return Route
     */
    public function map($pattern, $callable)
    {
        return $this->routes[] = new Route($pattern, $callable);
    }

    /**
     * Maps a GET request to a callable.
     *
     * @param  string $pattern
     * @param  mixed  $callable
     * @return Route
     */
    public function get($pattern, $callable)
    {
        return $this->map($pattern, $callable)->setMethods('GET');
    }

    /**
     * Maps a POST request to a callable.
     *
     * @param  string $pattern
     * @param  mixed  $callable
     * @return Route
     */
    public function post($pattern, $callable)
    {
        return $this->map($pattern, $callable)->setMethods('POST');
    }

    /**
     * Maps a PUT request to a callable.
     *
     * @param  string $pattern
     * @param  mixed  $callable
     * @return Route
     */
    public function put($pattern, $callable)
    {
        return $this->map($pattern, $callable)->setMethods('PUT');
    }

    /**
     * Maps a DELETE request to a callable.
     *
     * @param  string $pattern
     * @param  mixed  $callable
     * @return Route
     */
    public function delete($pattern, $callable)
    {
        return $this->map($pattern, $callable)->setMethods('DELETE');
    }

    /**
     * Maps a PATCH request to a callable.
     *
     * @param  string $pattern
     * @param  mixed  $callable
     * @return Route
     */
    public function patch($pattern, $callable)
    {
        return $this->map($pattern, $callable)->setMethods('PATCH');
    }

    /**
     * Gets the routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        foreach ($this->controllers as $controller) {
            foreach ($controller->getRoutes() as $config) {
                if (isset($config[0]) && isset($config[1])) {

                    $route = $this->map($config[0], array($controller, $config[1]));

                    if (isset($config[2])) {
                        $route->setMethods($config[2]);
                    }

                    if (isset($config[3])) {
                        $route->setOptions($config[3]);
                    }

                    $this->routes[] = $route;
                }
            }
        }

        return $this->routes;
    }

    /**
     * Gets the controller arguments.
     *
     * @param  Request $request
     * @param  mixed   $controller
     * @return array
     */
    public function getArguments(Request $request, $controller)
    {
        if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && !$controller instanceof \Closure) {
            $r = new \ReflectionObject($controller);
            $r = $r->getMethod('__invoke');
        } else {
            $r = new \ReflectionFunction($controller);
        }

        return $this->doGetArguments($request, $controller, $r->getParameters());
    }

    /**
     * Gets controller arguments and automatically sets values.
     *
     * @param  Request                $request
     * @param  mixed                  $controller
     * @param  \ReflectionParameter[] $parameters
     * @return array
     */
    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        $arguments = array();

        foreach ($parameters as $param) {

            if ($param->getClass() && $param->getClass()->isInstance($this->app)) {
                $arguments[] = $this->app;
            } elseif (($value = $request->get($param->name)) !== null) {
                $arguments[] = $value;
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {

                if (is_array($controller)) {
                    $ctrl = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                } elseif (is_object($controller)) {
                    $ctrl = get_class($controller);
                } else {
                    $ctrl = $controller;
                }

                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument.', $ctrl, $param->name));
            }
        }

        return $arguments;
    }
}
