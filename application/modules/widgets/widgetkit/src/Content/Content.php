<?php

namespace YOOtheme\Widgetkit\Content;

class Content implements ContentInterface
{
    protected $id;
    protected $name;
    protected $type;
    protected $typeObject;
    protected $data = array();

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeObject()
    {
        return $this->typeObject;
    }

    /**
     * @param TypeInterface $typeObject
     */
    public function setTypeObject(TypeInterface $typeObject)
    {
        $this->typeObject = $typeObject;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data, true) ?: array();
        }

        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->typeObject ? $this->typeObject->getItems($this) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array('id' => $this->id, 'name' => $this->name, 'type' => $this->type, 'data' => $this->getData());
    }

    /**
     * Checks if a key exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        $this->getData();

        return isset($this->data[$key]);
    }

    /**
     * Gets a value by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        $this->getData();

        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Sets a value.
     *
     * @param string $key
     * @param string $value
     */
    public function offsetSet($key, $value)
    {
        $this->getData();

        $this->data[$key] = $value;
    }

    /**
     * Unset a value.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        $this->getData();

        unset($this->data[$key]);
    }

    /**
     * Truncates text.
     *
     * @param  string $text
     * @param  int    $length
     * @return string
     */
    public static function truncate($text, $length = 100)
    {
        $text = strip_tags($text);

        if (function_exists('mb_strpos')) {
            if (($pos = @mb_strpos($text, ' ', $length)) > 0) {
                $text = mb_substr($text, 0, $pos) . '...';
            }
        } else {
            if (($pos = @strpos($text, ' ', $length)) > 0) {
                $text = substr($text, 0, $pos) . '...';
            }
        }

        return $text;
    }
}
