<?php

namespace YOOtheme\Framework\View\Asset\Filter;

use YOOtheme\Framework\View\Asset\AssetInterface;

interface FilterInterface
{
    /**
     * Filter content callback.
     *
     * @param AssetInterface $asset
     */
    public function filterContent(AssetInterface $asset);
}
