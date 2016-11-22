<?php

namespace YOOtheme\Widgetkit\Image;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class ImageProvider extends ApplicationAware
{
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
     * @param  string $file
     * @return Image|false
     */
    public function create($file)
    {
        $base = $this['request']->getBasePath();

        if (preg_match('/^https?:/i', $file)) {
            $file = str_replace($this['request']->getBaseUrl(), $base, $file);
        }

        if (!$this->isAbsolutePath($file)) {
            $file = "{$base}/{$file}";
        }

        if (!preg_match('/\.(gif|png|jpe?g)$/i', $file) || !file_exists($file) || ($base && !strpos($file, $base) === 0)) {
            return false;
        }

        return new Image($this->app, $file);
    }

    /**
     * Get image thumbnail url.
     *
     * @param  string $file
     * @param  string $width
     * @param  string $height
     * @return string
     */
    public function thumbnailUrl($file, $width, $height) {

        if ($image = $this->create($file)) {

            $image->setOptions(array('w' => $width, 'h' => $height));

            return $image->cache();
        }

        return $file;
    }

    /**
     * Returns true if the file is an existing absolute path.
     *
     * @param  string $file
     * @return boolean
     */
    protected static function isAbsolutePath($file)
    {
        return $file[0] == '/' || $file[0] == '\\' || (strlen($file) > 3 && ctype_alpha($file[0]) && $file[1] == ':' && ($file[2] == '\\' || $file[2] == '/')) || null !== parse_url($file, PHP_URL_SCHEME);
    }
}
