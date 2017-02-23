<?php

namespace YOOtheme\Framework\View\Helper;

class AttributeHelper
{
    /**
     * Render shortcut.
     *
     * @see render()
     */
    public function __invoke($attrs)
    {
        return $this->render($attrs);
    }

    /**
     * Renders html attributes.
     *
     * @param  array $attrs
     * @return string
     */
    public function render($attrs)
    {
        $html  = array();
        $attrs = call_user_func_array('array_merge', func_get_args());

        foreach ($attrs as $key => $value) {

            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            if (is_numeric($key)) {
               $html[] = $value;
            } elseif ($value === true) {
               $html[] = $key;
            } elseif ($value !== '') {
               $html[] = sprintf('%s="%s"', $key, htmlspecialchars($value));
            }
        }

        return $html ? ' '.implode(' ', $html) : '';
    }
}
