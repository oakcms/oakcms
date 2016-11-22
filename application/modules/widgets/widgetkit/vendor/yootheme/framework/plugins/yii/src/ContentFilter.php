<?php

namespace YOOtheme\Framework\Yii;

use YOOtheme\Framework\Filter\FilterInterface;

class ContentFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return $value;
    }
}
