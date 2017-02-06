<?php

namespace YOOtheme\Widgetkit\Framework\Resource;

class ResourceLocator implements LocatorInterface
{
    /**
     * @var array
     */
    protected $paths = array();

    /**
     * Add path(s) to locator.
     *
     * @param  string       $prefix
     * @param  string|array $paths
     * @return self
     */
    public function addPath($prefix, $paths)
    {
        $paths = array_map(function($path) use ($prefix) {
            return array($prefix, rtrim(strtr($path, '\\', '/'), '\/'));
        }, (array) $paths);

        $this->paths = array_merge($paths, $this->paths);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function find($resource)
    {
        return $this->findResources($resource, true);
    }

    /**
     * {@inheritdoc}
     */
    public function findVariants($resource)
    {
        return $this->findResources($resource);
    }

    /**
     * Find the first resource or all resource variants.
     *
     * @param  string $resource
     * @param  bool   $first
     * @return array|string|false
     */
    protected function findResources($resource, $first = false)
    {
        $file  = ltrim(strtr($resource, '\\', '/'), '\/');
        $paths = $first ? false : array();

        foreach ($this->paths as $parts) {

            list($prefix, $path) = $parts;

            if ($length = strlen($prefix) and 0 !== strpos($file, $prefix)) {
                continue;
            }

            if (($p = substr($file, $length)) !== false) {
                $path .= '/'.ltrim($p, '\/');
            }

            if (file_exists($path)) {

                if ($first) {
                    return $path;
                }

                $paths[] = $path;
            }
        }

        return $paths;
    }
}
