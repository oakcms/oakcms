<?php

namespace YOOtheme\Widgetkit\Framework\View\Asset\Filter;

use YOOtheme\Widgetkit\Framework\View\Asset\AssetInterface;

interface FilterInterface
{
    /**
     * Filter content callback.
     *
     * @param AssetInterface $asset
     */
    public function filterContent(AssetInterface $asset);
}
