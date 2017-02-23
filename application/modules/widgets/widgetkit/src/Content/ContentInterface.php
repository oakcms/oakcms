<?php

namespace YOOtheme\Widgetkit\Content;

interface ContentInterface extends \ArrayAccess
{
    /**
     * Gets the id.
     *
     * @return string
     */
    public function getId();

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the type.
     *
     * @return string
     */
    public function getType();

    /**
     * Gets the type object.
     *
     * @return TypeInterface
     */
    public function getTypeObject();

    /**
     * Gets the data.
     *
     * @return array
     */
    public function getData();

    /**
     * Gets the items.
     *
     * @return ItemCollection
     */
    public function getItems();

    /**
     * Gets the type data as array.
     *
     * @return array
     */
    public function toArray();
}
