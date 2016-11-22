<?php

namespace YOOtheme\Widgetkit\Content;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\Plugin\Plugin;

class Type extends Plugin implements TypeInterface
{
    /**
     * @var callable
     */
    protected $items;

    /**
     * {@inheritdoc}
     */
    public function main(Application $app)
    {
        if (isset($this->config['icon'])) {
            $this->config['icon'] = $app['url']->to($this->config['icon']);
        }

        if ($name = (string) $this->getConfig('name')) {
            $app['types'][$name] = $this;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($name = null)
    {
        if ($name === null) {
            return $this->config;
        } elseif (array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(ContentInterface $content)
    {
        $items = new ItemCollection($this->app);

        if (is_callable($this->items)) {
            call_user_func($this->items, $items, $content, $this->app);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->getConfig();
    }
}
