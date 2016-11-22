<?php

namespace YOOtheme\Framework\Joomla;

use YOOtheme\Framework\Filter\FilterInterface;

class ContentFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return \JHtmlContent::prepare($value);
    }
}
