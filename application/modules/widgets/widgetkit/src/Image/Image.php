<?php

namespace YOOtheme\Widgetkit\Image;

use abeautifulsite\SimpleImage;
use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class Image extends ApplicationAware
{
    /**
     * @var string[]
     */
    protected $defaults = array('w' => '', 'h' => '', 'strategy' => '');

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string[]
     */
    protected $options = array();

    /**
     * Constructor.
     *
     * @param Application $app
     * @param string      $file
     */
    public function __construct(Application $app, $file)
    {
        $this->app  = $app;
        $this->file = $file;
    }

    /**
     * @return string[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param  string[] $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = array_intersect_key(array_replace($this->defaults, $options), $this->defaults);
        return $this;
    }

    /**
     * @return string
     */
    public function getPathName()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this['url']->to($this->file);
    }

    /**
     * @param  string|string[] $densities
     * @return Image[]
     */
    public function getSrcSets($densities = '2x')
    {
        $result = array();
        foreach((array) $densities as $density) {
            $result[$density] = $this['image']->create(preg_replace('/(.*?)(\.[^\.]+)?$/i', "$1-$density$2", $this->file, 1));
        }
        return array_filter($result);
    }

    /**
     * @param  string|string[] $densities
     * @return string[]
     */
    public function getSrcSetUrls($densities = '2x')
    {
        $srcSets = $this->getSrcSets($densities);
        array_walk($srcSets, function(&$img, $density) {
            $img = $img->getUrl().' '.$density;
        });
        return $srcSets;
    }

    /**
     * @return string
     */
    public function thumbnail()
    {
        $this->options['strategy'] = 'thumbnail';
        return $this->cache();
    }

    /**
     * @return string
     */
    public function bestFit()
    {
        $this->options['strategy'] = 'best_fit';
        return $this->cache();
    }

    /**
     * @return string
     */
    public function resize()
    {
        $this->options['strategy'] = 'resize';
        return $this->cache();
    }

    /**
     * @param  bool $save
     * @return string
     */
    public function cache($save = false)
    {
        if (!file_exists($cache = $this->getCacheName())) {

            if (!$save) {
                $file = ltrim(substr($this->file, strlen($this['request']->getBasePath())), '/');
                return $this['url']->route('image', array_merge($this->options, array('file' => $file, 'hash' => $this->getHash())));
            }

            $this->create()->save($cache);
        }

        return $this['url']->to($cache);
    }

    /**
     * @param string $format
     * @param string $quality
     */
    public function output($format = null, $quality = null)
    {
        $this->create()->output($format, $quality);
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return md5($this->file.';'.implode(';', $this->options).';'.$this['csrf']->generate());
    }

    /**
     * @return string
     */
    protected function getCacheName()
    {
        return sprintf('%s/%s-%s.%s',
            $this['path.cache'],
            pathinfo($this->file, PATHINFO_FILENAME),
            md5(filemtime($this->file).filesize($this->file).implode(';', $this->options)),
            pathinfo($this->file, PATHINFO_EXTENSION)
        );
    }

    /**
     * @return SimpleImage
     */
    protected function create()
    {
        $image = new SimpleImage($this->file);

        switch($this->options['strategy']) {
            case 'thumbnail':
                $width  = $this->options['w'] ?: $image->get_width();
                $height = $this->options['h'] ?: $image->get_height();
                $image->resize($width, $height);
                break;
            case 'best_fit':
                $width  = $this->options['w'] ?: $image->get_width();
                $height = $this->options['h'] ?: $image->get_height();
                if (is_numeric($width) && is_numeric($height)) {
                    $image->best_fit($width, $height);
                }
                break;
            default:
                $width  = $this->options['w'];
                $height = $this->options['h'];
                if (is_numeric($width) && is_numeric($height)) {
                    $image->thumbnail($width, $height);
                } elseif(is_numeric($width)) {
                    $image->fit_to_width($width);
                } elseif(is_numeric($height)) {
                    $image->fit_to_height($height);
                } else {
                    $width  = $image->get_width();
                    $height = $image->get_height();
                    $image->thumbnail($width, $height);
                }
        }

        return $image;
    }
}
