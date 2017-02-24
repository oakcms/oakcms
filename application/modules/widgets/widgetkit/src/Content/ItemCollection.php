<?php

namespace YOOtheme\Widgetkit\Content;

use YOOtheme\Widgetkit\Framework\Application;

class ItemCollection extends \ArrayObject
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function add(array $data)
    {
        $this->append(new Item($this->app, $data));
    }
}
