<?php

namespace YOOtheme\Widgetkit\Content;

use YOOtheme\Framework\Application;

class Item implements ItemInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param Application $app
     * @param array       $data
     */
    public function __construct(Application $app, array $data)
    {
        $this->app  = $app;
        $this->data = $data;
    }

    /**
    * Get a value or option by key.
    *
    * @param  string $key
    * @param  mixed  $default
    * @return mixed
    */
    public function get($key, $default = null)
    {

        if (strpos($key, '.') === false) {
            return isset($this->data[$key]) ? $this->data[$key] : $default;
        }

        list($key, $option) = explode('.', $key, 2);

        if (isset($this->data[$key]) && is_array($this->data[$key]) && isset($this->data[$key][$option])) {
            return $this->data[$key][$option];
        }

        if (isset($this->data['options'], $this->data['options'][$key]) && is_array($this->data['options'][$key]) && isset($this->data['options'][$key][$option])) {
            return $this->data['options'][$key][$option];
        }

        return $default;
    }

    /**
     * Get escaped value or option by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function escape($key)
    {
        if ($value = $this->get($key)) {

            $value = htmlspecialchars($value);

            // email cloaking fix
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $value = str_replace('@', '&#064;', $value);
            }

            return $value;
        }
    }

    /**
     * Get image tag for a url.
     *
     * @param  string $key
     * @param  array  $attrs
     * @return bool
     */
    public function img($key, array $attrs = array())
    {
        if ($value = $this->get($key)) {

            $value = str_replace(' ', '%20', $value);

            if ($image = $this->app['image']->create($value) and $srcSets = $image->getSrcSetUrls(array('2x', '3x'))) {
                $attrs['srcset'] = implode(',', $srcSets);
            }

            return sprintf('<img%s>', $this->attrs(array('src' => $value), $attrs));
        }
    }

    /**
     * Get image thumbnail tag for a url.
     *
     * @param  string $key
     * @param  string $width
     * @param  string $height
     * @param  array  $attrs
     * @param  bool   $url
     * @return string
     */
    public function thumbnail($key, $width, $height, array $attrs = array(), $url = false)
    {
        if ($value = $this->get($key)) {

            if ($image = $this->app['image']->create($value)) {

                $image->setOptions(array('w' => $width, 'h' => $height));

                if ($srcSets = $image->getSrcSets(array('2x', '3x'))) {

                    array_walk($srcSets, function(&$img, $density) use ($width, $height) {
                        $img = $img->setOptions(array('w' => $width * $density[0], 'h' => $height * $density[0]))->thumbnail() . ' ' . $density;
                    });

                    $attrs['srcset'] = implode(',', $srcSets);
                }

                $value = $image->cache();
            }

            return $url ? $value : sprintf('<img%s>', $this->attrs(array('src' => $value), $attrs));
        }
    }

    /**
     * Get video tag for a url.
     *
     * @param  string $key
     * @param  array  $attrs
     * @return bool
     */
    public function video($key, array $attrs = array())
    {
        if ($value = $this->get($key)) {
            return sprintf('<video%s></video>', $this->attrs(array('src' => $value), $attrs));
        }
    }

    /**
     * Get audio tag for a url.
     *
     * @param  string $key
     * @param  array  $attrs
     * @return bool
     */
    public function audio($key, array $attrs = array())
    {
        if ($value = $this->get($key)) {
            return sprintf('<audio%s></audio>', $this->attrs(array('src' => $value), $attrs));
        }
    }

    /**
     * Get image, video or audio tag for a url.
     *
     * @param  string $key
     * @param  array  $attrs
     * @return bool
     */
    public function media($key, array $attrs = array())
    {
        switch($this->type($key)) {
            case 'image':
                return $this->img($key, $attrs);

            case 'video':
                return $this->video($key, $attrs);

            case 'audio':
                return $this->audio($key, $attrs);

            case 'iframe':

                $src    = $this->get($key);
                $scheme = parse_url($src);

                if (preg_match('/(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/i', $src, $m) || preg_match('/(\/\/.*?youtu\.be)\/([^\?]+)(.*)/i', $src, $m)) {
                    $src = '//www.youtube.com/embed/'.$m[2].(strpos($m[3], '?')===false ? '?':'').$m[3];
                    $src .= '&wmode=transparent';
                } else if (preg_match('/(\/\/.*?)vimeo\.[a-z]+\/(?:\w*\/)*(\d+)/i', $src, $m)) {
                    $src = '//player.vimeo.com/video/'.$m[2].(isset($scheme['query']) && $scheme['query'] ? '?'.$scheme['query']: '');
                }

                return sprintf('<iframe%s></iframe>', $this->attrs(array('src' => $src, 'allowfullscreen'), $attrs));
        }
    }

    /**
     * Get media type for a url.
     *
     * @param  string $key
     * @return string
     */
    public function type($key)
    {
        if (!$value = $this->get($key) or !is_string($value)) {
            return;
        }

        $url = array_merge(array('host' => '', 'path' => ''), parse_url($value));

        if (preg_match('/\.(gif|png|jpe?g|svg)$/i', $url['path'])) {
            return 'image';
        } else if (preg_match('/\.(mp4|ogv|webm)$/i', $url['path'])) {
            return 'video';
        } else if (preg_match('/\.(mp3|ogg|wav)$/i', $url['path'])) {
            return 'audio';
        } else if (preg_match('/(vimeo\.com|youtu(be\.com|\.be))$/i', $url['host'])) {
            return 'iframe';
        }
    }

    /**
     * Get tag attributes form array.
     *
     * @param  array $attrs
     * @return string
     */
    public function attrs($attrs)
    {
        $html  = array();
        $attrs = call_user_func_array('array_merge', func_get_args());

        foreach ($attrs as $key => $value) {

            if (is_numeric($key)) {
               $html[] = $value;
            } elseif ($value === true) {
               $html[] = $key;
            } elseif ($value !== '') {
               $html[] = sprintf('%s="%s"', $key, htmlspecialchars($value, null, null, false));
            }

        }

        return $html ? ' '.implode(' ', $html) : '';
    }

    /**
     * Checks if a key exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Gets a value by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Sets a value.
     *
     * @param string $key
     * @param string $value
     */
    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Unset a value.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Returns an iterator for item keys.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_keys($this->data));
    }
}
