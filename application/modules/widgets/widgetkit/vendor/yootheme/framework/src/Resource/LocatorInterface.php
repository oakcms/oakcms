<?php

namespace YOOtheme\Framework\Resource;

interface LocatorInterface
{
    /**
     * Find a resource.
     *
     * @param  string $resource
     * @return string|false
     */
    public function find($resource);

    /**
     * Find the resource variants.
     *
     * @param  string $resource
     * @return array
     */
    public function findVariants($resource);
}
