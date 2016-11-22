<?php

namespace YOOtheme\Widgetkit\Widget;

use YOOtheme\Widgetkit\Content\ContentInterface;

interface WidgetInterface
{
    /**
     * Gets the config or config value.
     *
     * @param  mixed $name
     * @return array
     */
    public function getConfig($name = null);

    /**
     * Renders the widget.
     *
     * @param  ContentInterface $content
     * @param  array            $settings
     * @return string
     */
    public function render(ContentInterface $content, array $settings);

    /**
     * Gets the type data as array.
     *
     * @return array
     */
    public function toArray();
}
