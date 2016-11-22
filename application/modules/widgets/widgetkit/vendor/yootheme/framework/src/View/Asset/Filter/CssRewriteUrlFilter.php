<?php

namespace YOOtheme\Framework\View\Asset\Filter;

use YOOtheme\Framework\Routing\UrlGenerator;
use YOOtheme\Framework\View\Asset\AssetInterface;

class CssRewriteUrlFilter implements FilterInterface
{
    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var string
     */
    protected $path;

    /**
     * Constructor.
     *
     * @param UrlGenerator $url
     */
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function filterContent(AssetInterface $asset)
    {
        // has path?
        if (!$path = $asset->getOption('path')) {
            return;
        }

        // set base path
        $this->path = dirname($this->url->to($path)).'/';

        $asset->setContent(preg_replace_callback('/url\(\s*[\'"]?(?![a-z]+:|\/+)([^\'")]+)[\'"]?\s*\)/i', array($this, 'rewrite'), $asset->getContent()));
    }

    /**
     * Rewrite url callback.
     *
     * @param  array $matches
     * @return string
     */
    protected function rewrite($matches)
    {
        // prefix with base and remove '../' segments if possible
        $path = $this->path.$matches[1];
        $last = '';

        while ($path != $last) {
            $last = $path;
            $path = preg_replace('`(^|/)(?!\.\./)([^/]+)/\.\./`', '$1', $path);
        }

        return 'url("'.$path.'")';
    }
}
