<?php

namespace YOOtheme\Framework\Plugin\Loader;

use YOOtheme\Framework\Application;

class EventLoader implements LoaderInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function load($name, array $config)
    {
        if (isset($config['events'])) {
            foreach ($config['events'] as $event => $listener) {
                $this->app->on($event, $listener);
            }
        }

        return $config;
    }
}
