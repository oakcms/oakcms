<?php

namespace YOOtheme\Framework\Routing;

use YOOtheme\Framework\Routing\Exception\HttpException;

class Router
{
    /**
     * @var ControllerCollection
     */
    protected $controllers;

    /**
     * Constructor.
     *
     * @param ControllerCollection $controllers
     */
    public function __construct(ControllerCollection $controllers)
    {
        $this->controllers = $controllers;
    }

    /**
     * Matches a route to a request.
     *
     * @param  Request $request
     * @return Route
     */
    public function matchRequest(Request $request)
    {
        $url = $request->get('p', 'index');

        foreach ($this->controllers->getRoutes() as $route) {

            if ($route->getMethods() && !in_array($request->getMethod(), $route->getMethods())) {
                continue;
            }

            if ($route->matches($url)) {

                if ($params = $route->getParams()) {
                    $request->add($params);
                }

                return $route;
            }
        }

        throw new HttpException(404, sprintf('No route found for "%s"', $url));
    }
}
