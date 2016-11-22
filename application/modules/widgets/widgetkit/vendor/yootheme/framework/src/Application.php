<?php

namespace YOOtheme\Framework;

use YOOtheme\Framework\Config\Config;
use YOOtheme\Framework\Csrf\DefaultCsrfProvider;
use YOOtheme\Framework\Event\Event;
use YOOtheme\Framework\Event\EventDispatcher;
use YOOtheme\Framework\Filter\FilterManager;
use YOOtheme\Framework\Plugin\PluginManager;
use YOOtheme\Framework\Plugin\Loader\EventLoader;
use YOOtheme\Framework\Resource\ResourceLocator;
use YOOtheme\Framework\Routing\ControllerCollection;
use YOOtheme\Framework\Routing\ExceptionListener;
use YOOtheme\Framework\Routing\Exception\HttpExceptionInterface;
use YOOtheme\Framework\Routing\Response;
use YOOtheme\Framework\Routing\ResponseListener;
use YOOtheme\Framework\Routing\Request;
use YOOtheme\Framework\Routing\Route;
use YOOtheme\Framework\Routing\Router;
use YOOtheme\Framework\Routing\RouterListener;
use YOOtheme\Framework\Translation\Translator;
use YOOtheme\Framework\User\AccessListener;

class Application extends Container
{
    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        parent::__construct();

        $this['events'] = function() {

            $events = new EventDispatcher;
            $events->subscribe(new AccessListener);
            $events->subscribe(new ExceptionListener);
            $events->subscribe(new ResponseListener);
            $events->subscribe(new RouterListener);

            return $events;
        };

        $this['plugins'] = function($app) {

            $manager = new PluginManager($app);
            $manager->addLoader(new EventLoader($app));
            $manager->addPath($app['path.vendor'].'/yootheme/framework/plugins/*/plugin.php');

            return $manager;
        };

        $this['router'] = function($app) {
            return new Router($app['controllers']);
        };

        $this['controllers'] = function($app) {
            return new ControllerCollection($app);
        };

        $this['csrf'] = function() {
            return new DefaultCsrfProvider();
        };

        $this['locator'] = function() {
            return new ResourceLocator();
        };

        $this['config'] = function() {
            return new Config();
        };

        $this['user'] = function($app) {
            return $app['users']->get();
        };

        $this['filter'] = function() {
            return new FilterManager();
        };

        $this['translator'] = function($app) {

            $translator = new Translator($app['locator']);

            if (isset($app['locale'])) {
                $translator->setLocale($app['locale']);
            }

            return $translator;
        };

        $values = array_replace(array(
            'app' => $this,
            'debug' => false,
            'version' => null),
        $values);

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Adds an event listener.
     *
     * @param string   $event
     * @param callable $listener
     * @param int      $priority
     */
    public function on($event, $listener, $priority = 0)
    {
        $this['events']->on($event, $listener, $priority);
    }

    /**
     * Triggers an event.
     *
     * @param  string $event
     * @param  array  $arguments
     * @return Event
     */
    public function trigger($event, array $arguments = array())
    {
        return $this['events']->trigger($event, $arguments);
    }

    /**
     * Loads all plugins and triggers 'boot' event.
     *
     * @return self
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;

        $this['plugins']->load();
        $this['events']->trigger('boot', array($this));

        return $this;
    }

    /**
     * Handles a request and converts it to a response.
     *
     * @param  Request $request
     * @param  bool    $send
     * @return Response|null
     */
    public function handle(Request $request = null, $send = true)
    {
        $request = $request ?: $this['request'];

        try {
            $response = $this->handleRaw($request);
        } catch (\Exception $e) {
            $response = $this->handleException($e, $request);
        }

        return $send ? $response->send() : $response;
    }

    /**
     * @param  Request $request
     * @return null|Response
     */
    protected function handleRaw(Request $request)
    {
        $response = null;
        $event    = $this['events']->trigger(new Event('request', compact('request')), array($this));

        if (isset($event['response'])) {

            $response = $event['response'];

        } else {

            $callable = $request->attributes->get('_callable');
            $response = call_user_func_array($callable, $this['controllers']->getArguments($request, $callable));
            $event    = $this['events']->trigger(new Event('response', compact('response', 'request')), array($this));

            if (isset($event['response'])) {
                $response = $event['response'];
            }
        }

        if (!$response instanceof Response) {
            throw new \LogicException('Response must be of type YOOtheme\Framework\Routing\Response.');
        }

        return $response;
    }

    /**
     * Handles an exception by trying to convert it to a Response.
     *
     * @param  \Exception $exception
     * @param  Request    $request
     * @throws \Exception
     * @return Response
     */
    protected function handleException(\Exception $exception, $request)
    {
        $event = $this['events']->trigger(new Event('exception', compact('exception', 'request')), array($this));
        $exception = $event['exception'];

        if (!isset($event['response'])) {
            throw $exception;
        }

        $response = $event['response'];

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatus($exception->getStatus());
        } else {
            $response->setStatus(500);
        }

        return $response;
    }
}
