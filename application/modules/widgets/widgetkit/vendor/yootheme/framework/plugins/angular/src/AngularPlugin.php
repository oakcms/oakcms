<?php

namespace YOOtheme\Framework\Angular;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;
use YOOtheme\Framework\Plugin\Plugin;
use YOOtheme\Framework\Routing\Exception\HttpException;

class AngularPlugin extends Plugin
{
    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var array
     */
    protected $templates = array();

    /**
     * {@inheritdoc}
     */
    public function main(Application $app)
    {
        $app['angular'] = $this;
        $app['controllers']->map('template', array($this, 'templateAction'));

        $app->on('boot', array($this, 'boot'));
    }

    /**
     * Callback for 'boot' event.
     */
    public function boot()
    {
        $path = sprintf('%s/yootheme/framework', $this['path.vendor']);

        // register library
        $this['scripts']->register('angular', $path.'/assets/angular/angular.min.js');
        $this['scripts']->register('angular-resource', $path.'/assets/angular-resource/angular-resource.min.js', array('angular'));
        $this['scripts']->register('angular-touch', $path.'/assets/angular-touch/angular-touch.min.js', array('angular'));
        $this['scripts']->register('application', $path.'/plugins/angular/lib/application.min.js', array('angular', 'application-config', 'application-templates'));
        $this['scripts']->register('application-translator', $path.'/plugins/angular/lib/translator.min.js', array('application'));

        // register config
        $this['app']->on('view', function($event, $app) {
            foreach ($app['angular']->getConfig() as $name => $values) {
                $app['scripts']->register("application-{$name}", sprintf('var %1$s = %1$s || {}; %1$s.%2$s = %3$s;', $app['angular']->get('name', $app['name']), $name, json_encode($values)), array(), 'string');
            }
        }, 10);
    }

    /**
     * Gets a config value.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }

    /**
     * Sets a config value.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return self
     */
    public function set($name, $value)
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * Gets all config values.
     *
     * @return array
     */
    public function getConfig()
    {
        $config = array_merge(array(
            'url'     => $this['url']->base(),
            'route'   => $this['url']->route(),
            'locale'  => $this['locale'],
            'locales' => $this['translator']->getResources()
        ), $this->config);

        $templates = array();

        foreach ($this->templates as $name => $template) {
            if ($template['cache']) {
                $templates[$name] = $this['view']->render($template['path']);
            }
        }

        return compact('config', 'templates');
    }

    /**
     * Gets a template.
     *
     * @param  string $name
     * @return string
     */
    public function getTemplate($name)
    {
        return isset($this->templates[$name]) ? $this->templates[$name]['path'] : null;
    }

    /**
     * Adds a template.
     *
     * @param  string $name
     * @param  string $path
     * @param  bool   $cache
     * @return self
     */
    public function addTemplate($name, $path, $cache = false)
    {
        $this->templates[$name] = compact('path', 'cache');

        return $this;
    }

    /**
     * Renders a template.
     *
     * @param  string $name
     * @return string
     */
    public function templateAction($name = null)
    {
        if ($name && $template = $this->getTemplate($name)) {
            return $this['response']->raw($this['view']->render($template));
        }

        throw new HttpException(404, 'Template not found.');
    }
}
