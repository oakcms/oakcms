<?php

namespace YOOtheme\Framework\Routing;

class Request
{
    /**
     * @var ParameterBag
     */
    public $attributes;

    /**
     * @var ParameterBag
     */
    public $request;

    /**
     * @var ParameterBag
     */
    public $server;

    /**
     * @var ParameterBag
     */
    public $cookies;

    /**
     * @var HeaderBag
     */
    public $headers;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $baseRoute;

    /**
     * Constructor.
     *
     * @param string $baseUrl
     * @param string $basePath
     * @param string $baseRoute
     * @param array  $request
     * @param array  $server
     */
    public function __construct($baseUrl, $basePath, $baseRoute, array $request = array(), array $server = array(), array $cookies = array())
    {
        $this->baseUrl    = $baseUrl;
        $this->basePath   = $basePath;
        $this->baseRoute  = $baseRoute;
        $this->attributes = new ParameterBag;
        $this->request    = new ParameterBag($request ?: $_REQUEST);
        $this->cookies    = new ParameterBag($cookies ?: $_COOKIE);
        $this->server     = new ServerBag($server ?: $_SERVER);
        $this->headers    = new HeaderBag($this->server->getHeaders());

        // decode json content type
        if (stripos($this->headers->get('CONTENT_TYPE'), 'application/json') !== false) {
            if ($json = json_decode(@file_get_contents('php://input'), true)) {
                $this->request->add($json);
            }
        }
    }

    /**
     * Gets the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets the base URL.
     *
     * @param  string $baseUrl
     * @return self
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Gets the base path.
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Sets the base path.
     *
     * @param  string $basePath
     * @return self
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * Gets the base route.
     *
     * @return string
     */
    public function getBaseRoute()
    {
        return $this->baseRoute;
    }

    /**
     * Sets the base route.
     *
     * @param  string $baseRoute
     * @return self
     */
    public function setBaseRoute($baseRoute)
    {
        $this->baseRoute = $baseRoute;

        return $this;
    }

    /**
     * Gets the HTTP method.
     *
     * @return string
     */
    public function getMethod()
    {
        $method = $this->server->get('REQUEST_METHOD', 'GET');
        $method = $this->headers->get('X-HTTP-Method-Override', $method);

        return strtoupper($method);
    }

    /**
     * Checks if this is an XHR request.
     *
     * @return bool
     */
    public function isXhr()
    {
        return $this->server->get('X_REQUESTED_WITH') == 'XMLHttpRequest';
    }

    /**
     * Proxy method calls to request parameter bag.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args) {
        return call_user_func_array(array($this->request, $method), $args);
    }
}
