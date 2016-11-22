<?php

namespace YOOtheme\Widgetkit\Content;

interface TypeInterface
{
    /**
     * Gets the config or config value.
     *
     * @param  mixed $name
     * @return array
     */
    public function getConfig($name = null);

    /**
     * Gets the items for this content type.
     *
     * @param  ContentInterface $content
     * @return ItemCollection
     */
    public function getItems(ContentInterface $content);

    /**
     * Gets the type data as array.
     *
     * @return array
     */
    public function toArray();
}
