<?php

namespace YOOtheme\Framework\Joomla;

use YOOtheme\Framework\Csrf\DefaultCsrfProvider;

class CsrfProvider extends DefaultCsrfProvider
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return \JFactory::getSession()->getToken();
    }
}
