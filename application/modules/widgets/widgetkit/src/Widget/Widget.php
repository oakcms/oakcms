<?php

namespace YOOtheme\Widgetkit\Widget;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\Plugin\Plugin;
use YOOtheme\Widgetkit\Content\ContentInterface;

class Widget extends Plugin implements WidgetInterface
{
    /**
     * {@inheritdoc}
     */
    public function main(Application $app)
    {
        if (isset($this->config['icon'])) {
            $this->config['icon'] = $app['url']->to($this->config['icon']);
        }

        if ($name = $this->getConfig('name')) {
            $app['widgets'][$name] = $this;
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
    public function render(ContentInterface $content, array $settings)
    {
        static $nesting = 0;

        $output = $nesting++ < 10 ? $this['view']->render($this->getConfig('view'), array('widget' => $this, 'items' => $content->getItems(), 'settings' => array_merge($this->getConfig('settings'), $settings))) : '';

        $nesting--;

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->getConfig();
    }
}
