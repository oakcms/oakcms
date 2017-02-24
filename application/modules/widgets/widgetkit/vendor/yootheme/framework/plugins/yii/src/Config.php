<?php

namespace YOOtheme\Framework\Yii;

class Config extends \YOOtheme\Framework\Config\Config
{

    /**
     * Gets a value.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->values[$key]['value']) ? $this->values[$key]['value'] : $default;
    }

}
