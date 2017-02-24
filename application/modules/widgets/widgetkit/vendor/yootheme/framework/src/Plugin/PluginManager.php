<?php

namespace YOOtheme\Framework\Plugin;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;
use YOOtheme\Framework\Plugin\Loader\LoaderInterface;

class PluginManager implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var LoaderInterface[]
     */
    protected $loaders = array();

    /**
     * @var array
     */
    protected $paths = array();

    /**
     * @var array
     */
    protected $plugins = array();

    /**
     * @var array
     */
    protected $registered = array();

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
     * Gets a plugin by name.
     *
     * @param  string $name
     * @return PluginInterface|null
     */
    public function get($name)
    {
        return isset($this->plugins[$name]) ? $this->plugins[$name] : null;
    }

    /**
     * Gets all plugins.
     *
     * @return array
     */
    public function all()
    {
        return $this->plugins;
    }

    /**
     * Loads plugins by name.
     *
     * @param string|array $plugins
     */
    public function load($plugins = array())
    {
        $resolved = array();

        if (is_string($plugins)) {
            $plugins = (array) $plugins;
        }

        $this->registerPlugins();

        if (!$plugins) {
            $plugins = array_keys($this->registered);
        }

        foreach ($plugins as $name) {
            $this->resolvePlugins($this->registered[$name], $resolved);
        }

        $resolved = array_diff_key($resolved, $this->plugins);

        foreach ($resolved as $name => $plugin) {

            $plugin = $this->loadPlugin($name, $plugin);

            if ($plugin instanceof ApplicationAware) {
                $plugin->setApplication($this->app);
            }

            $plugin->main($this->app);
        }
    }

    /**
     * Adds a plugin config loader.
     *
     * @param  LoaderInterface $loader
     * @return self
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;

        return $this;
    }

    /**
     * Adds a plugin path(s).
     *
     * @param  string|array $paths
     * @return self
     */
    public function addPath($paths)
    {
        $this->paths = array_merge($this->paths, (array) $paths);

        return $this;
    }

    /**
     * Checks if a plugin exists.
     *
     * @param  string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->plugins[$name]);
    }

    /**
     * Gets a plugin by name.
     *
     * @param  string $name
     * @return bool
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Sets a plugin.
     *
     * @param string $name
     * @param string $plugin
     */
    public function offsetSet($name, $plugin)
    {
        $this->plugins[$name] = $plugin;
    }

    /**
     * Unset a plugin.
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->plugins[$name]);
    }

    /**
     * Implements the IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->plugins);
    }

    /**
     * Loads a plugin.
     *
     * @param  string $name
     * @param  array  $plugin
     * @return array
     */
    protected function loadPlugin($name, $plugin)
    {
        foreach ($this->loaders as $loader) {
            $plugin = $loader->load($name, $plugin);
        }

        if (isset($plugin['autoload'])) {
            foreach ($plugin['autoload'] as $namespace => $path) {
                $this->app['autoloader']->addPsr4($namespace, $this->resolvePath($plugin, $path));
            }
        }

        $class = is_string($plugin['main']) ? $plugin['main'] : 'YOOtheme\\Framework\\Plugin\\Plugin';

        return $this->plugins[$name] = new $class($plugin);
    }

    /**
     * Register plugins from paths.
     */
    protected function registerPlugins()
    {
        $includes = array();

        foreach ($this->paths as $path) {

            $paths = glob($path, GLOB_NOSORT) ?: array();

            foreach ($paths as $p) {

                if (!is_array($plugin = include $p) || !isset($plugin['name'])) {
                    continue;
                }

                if (!isset($plugin['main'])) {
                    $plugin['main'] = null;
                }

                $plugin['path'] = strtr(dirname($p), '\\', '/');

                if (isset($plugin['include'])) {
                    foreach ((array) $plugin['include'] as $include) {
                        $includes[] = $this->resolvePath($plugin, $include);
                    }
                }

                $this->registered[$plugin['name']] = $plugin;
            }
        }

        if ($this->paths = $includes) {
            $this->registerPlugins();
        }
    }

    /**
     * Resolves plugin requirements.
     *
     * @param array $plugin
     * @param array $resolved
     * @param array $unresolved
     *
     * @throws \RuntimeException
     */
    protected function resolvePlugins($plugin, &$resolved = array(), &$unresolved = array())
    {
        $unresolved[$plugin['name']] = $plugin;

        if (isset($plugin['require'])) {
            foreach ((array) $plugin['require'] as $required) {
                if (!isset($resolved[$required])) {

                    if (isset($unresolved[$required])) {
                        throw new \RuntimeException(sprintf('Circular requirement "%s > %s" detected.', $plugin['name'], $required));
                    }

                    if (isset($this->registered[$required])) {
                        $this->resolvePlugins($this->registered[$required], $resolved, $unresolved);
                    }
                }
            }
        }

        $resolved[$plugin['name']] = $plugin;
        unset($unresolved[$plugin['name']]);
    }

    /**
     * Resolves a path to a absolute plugin path.
     *
     * @param  array  $plugin
     * @param  string $path
     * @return string
     */
    protected function resolvePath($plugin, $path)
    {
        $path = strtr($path, '\\', '/');

        if (!($path[0] == '/' || (strlen($path) > 3 && ctype_alpha($path[0]) && $path[1] == ':' && $path[2] == '/'))) {
            $path = $plugin['path']."/$path";
        }

        return $path;
    }
}
