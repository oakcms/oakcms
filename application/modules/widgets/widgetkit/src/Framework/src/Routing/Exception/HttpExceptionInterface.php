<?php

namespace YOOtheme\Widgetkit\Framework\Routing\Exception;

/**
 * @author Kris Wallsmith <kris@symfony.com>
 */
interface HttpExceptionInterface
{
    /**
     * Returns the status code.
     *
     * @return int
     */
    public function getStatus();
}
