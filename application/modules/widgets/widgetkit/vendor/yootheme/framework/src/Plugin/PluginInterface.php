<?php

namespace YOOtheme\Framework\Plugin;

use YOOtheme\Framework\Application;

interface PluginInterface
{
    /**
     * Main bootstrap method.
     *
     * @param Application $app
     */
    public function main(Application $app);
}
