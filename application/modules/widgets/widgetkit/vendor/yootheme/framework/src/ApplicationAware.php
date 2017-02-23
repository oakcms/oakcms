<?php

namespace YOOtheme\Framework;

abstract class ApplicationAware implements \ArrayAccess
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Gets the application.
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Sets the application.
     *
     * @param Application $app
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Whether an application parameter or an object exists.
     *
     * @param  string $offset
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return isset($this->app[$offset]);
    }

    /**
     * Gets an application parameter or an object.
     *
     * @param  string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->app[$offset];
    }

    /**
     * Sets an application parameter or an object.
     *
     * @param  string $offset
     * @param  mixed  $value
     */
    public function offsetSet($offset, $value)
    {
        $this->app[$offset] = $value;
    }

    /**
     * Unsets an application parameter or an object.
     *
     * @param  string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->app[$offset]);
    }
}
