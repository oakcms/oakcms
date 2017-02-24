<?php

namespace YOOtheme\Framework\Event;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var string
     */
    protected $event;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var array
     */
    protected $sorted = array();

    /**
     * Constructor.
     *
     * @param string $event
     */
    public function __construct($event = 'YOOtheme\Framework\Event\Event')
    {
        $this->event = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function on($event, $listener, $priority = 0)
    {
        $this->listeners[$event][$priority][] = $listener;
        unset($this->sorted[$event]);
    }

    /**
     * {@inheritdoc}
     */
    public function off($event, $listener = null)
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        if ($listener === null) {
            unset($this->listeners[$event], $this->sorted[$event]);
            return;
        }

        foreach ($this->listeners[$event] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->listeners[$event][$priority][$key], $this->sorted[$event]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $event => $params) {
            if (is_string($params)) {
                $this->on($event, array($subscriber, $params));
            } elseif (is_string($params[0])) {
                $this->on($event, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    $this->on($event, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $event => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->off($event, array($subscriber, $listener[0]));
                }
            } else {
                $this->off($event, array($subscriber, is_string($params) ? $params : $params[0]));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function trigger($event, array $arguments = array())
    {
        if (is_string($event)) {
            $e = new $this->event($event);
        } elseif (is_a($event, $this->event)) {
            $e = $event;
        } else {
            throw new \RuntimeException(sprintf('Event must be an instance of "%s"', $this->event));
        }

        array_unshift($arguments, $e);

        foreach ($this->getListeners($e->getName()) as $listener) {

            call_user_func_array($listener, $arguments);

            if ($e->isPropagationStopped()) {
                break;
            }
        }

        return $e;
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($event = null)
    {
        return (bool) count($this->getListeners($event));
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($event = null)
    {
        if ($event !== null) {
            return isset($this->sorted[$event]) ? $this->sorted[$event] : $this->sortListeners($event);
        }

        foreach (array_keys($this->listeners) as $event) {
            if (!isset($this->sorted[$event])) {
                $this->sortListeners($event);
            }
        }

        return array_filter($this->sorted);
    }

    /**
     * Sorts all listeners of an event by their priority.
     *
     * @param  string $event
     * @return array
     */
    protected function sortListeners($event)
    {
        $sorted = array();

        if (isset($this->listeners[$event])) {
            krsort($this->listeners[$event]);
            $sorted = call_user_func_array('array_merge', $this->listeners[$event]);
        }

        return $this->sorted[$event] = $sorted;
    }
}
