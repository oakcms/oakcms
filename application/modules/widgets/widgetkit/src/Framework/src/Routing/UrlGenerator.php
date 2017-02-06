<?php

namespace YOOtheme\Widgetkit\Framework\Routing;

use YOOtheme\Widgetkit\Framework\Resource\LocatorInterface;

class UrlGenerator
{
    /**
     * Generates an absolute URL, e.g. "http://example.com/dir/file".
     */
    const ABSOLUTE_URL = true;

    /**
     * Generates an absolute path, e.g. "/dir/file".
     */
    const ABSOLUTE_PATH = false;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * Constructor.
     *
     * @param Request          $request
     * @param LocatorInterface $locator
     */
    public function __construct(Request $request, LocatorInterface $locator)
    {
        $this->request = $request;
        $this->locator = $locator;
    }

    /**
     * Get the base path.
     *
     * @param  mixed $referenceType
     * @return string
     */
    public function base($referenceType = self::ABSOLUTE_PATH)
    {
        if ($referenceType === self::ABSOLUTE_PATH) {
            return parse_url($this->request->getBaseUrl(), PHP_URL_PATH) ?: '';
        }

        return $this->request->getBaseUrl();
    }

    /**
     * Get the URL to a path.
     *
     * @param  string $path
     * @param  array  $parameters
     * @param  mixed  $referenceType
     * @return string
     */
    public function to($path, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $basePath = strtr($this->request->getBasePath(), '\\', '/');

        if ($query = substr(strstr($path, '?'), 1)) {
            parse_str($query, $params);
            $path = strstr($path, '?', true);
            $parameters = array_replace($parameters, $params);
        }

        if ($query = http_build_query($parameters)) {
            $query = '?'.$query;
        }

        if ($path and !$this->isAbsolutePath($path)) {
            $path = $this->locator->find($path) ?: $path;
        }

        $path = strtr($path, '\\', '/');

        if ($basePath && strpos($path, $basePath) === 0) {
            $path = ltrim(substr($path, strlen($basePath)), '/');
        }

        if ($path and preg_match('/^(?!\/|[a-z]+:\/\/)/i', $path)) {
            $path = $this->base($referenceType).'/'.$path;
        }

        return $path.$query;
    }

    /**
     * Gets the URL to a route.
     *
     * @param  string $pattern
     * @param  array  $parameters
     * @param  mixed  $referenceType
     * @return string
     */
    public function route($pattern = '', $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        throw new \BadMethodCallException('Must be implemented.');
    }

    /**
     * Returns true if the file is an absolute path.
     *
     * @param  string $file
     * @return boolean
     */
    protected function isAbsolutePath($file)
    {
        return $file[0] == '/' || $file[0] == '\\' || (strlen($file) > 3 && ctype_alpha($file[0]) && $file[1] == ':' && ($file[2] == '\\' || $file[2] == '/')) || null !== parse_url($file, PHP_URL_SCHEME);
    }
}
