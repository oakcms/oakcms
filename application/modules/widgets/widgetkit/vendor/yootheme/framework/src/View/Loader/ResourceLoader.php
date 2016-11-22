<?php

namespace YOOtheme\Framework\View\Loader;

use YOOtheme\Framework\Resource\LocatorInterface;

class ResourceLoader implements LoaderInterface
{
    protected $locator;

    /**
     * Constructor.
     *
     * @param LocatorInterface $locator
     */
    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function load($name)
    {
        $name = (string) $name;

        if (self::isAbsolutePath($name) && @is_file($name)) {
            return $name;
        }

        return $this->locator->find($name);
    }

    /**
     * Returns true if the file is an absolute path.
     *
     * @param  string $file
     * @return boolean
     */
    protected static function isAbsolutePath($file)
    {
        return $file[0] == '/' || $file[0] == '\\' || (strlen($file) > 3 && ctype_alpha($file[0]) && $file[1] == ':' && ($file[2] == '\\' || $file[2] == '/')) || null !== parse_url($file, PHP_URL_SCHEME);
    }
}
