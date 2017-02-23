<?php

namespace YOOtheme\Widgetkit\Framework\Plugin;

use YOOtheme\Widgetkit\Framework\Application;

interface PluginInterface
{
    /**
     * Main bootstrap method.
     *
     * @param Application $app
     */
    public function main(Application $app);
}
