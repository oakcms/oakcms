<?php

namespace YOOtheme\Framework\View\Helper;

class MacroHelper
{
    /**
     * @var array
     */
    protected $macros = array();

    /**
     * Adds or renders a macro.
     *
     * @param string         $name
     * @param array|callable $arg
     */
    public function __invoke($name, $arg = array())
    {
        if (is_array($arg)) {
            return $this->render($name, $arg);
        }

        if (is_callable($arg)) {
            $this->add($name, $arg);
        }
    }

    /**
     * Adds a macro.
     *
     * @param string   $name
     * @param callable $callable
     */
    public function add($name, $callable)
    {
        if (!isset($this->macros[$name]) && is_callable($callable)) {
            $this->macros[$name] = $callable;
        }
    }

    /**
     * Renders a macro.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public function render($name, array $args = array())
    {
        if (isset($this->macros[$name])) {

            ob_start();
            $output = call_user_func_array($this->macros[$name], $args);

            return ob_get_clean().$output;
        }
    }
}
