<?php

$config = array(

    'name' => 'framework/joomla',

    'main' => 'YOOtheme\\Framework\\Joomla\\JoomlaPlugin',

    'autoload' => array(

        'YOOtheme\\Framework\\Joomla\\' => 'src'

    )

);

return defined('_JEXEC') ? $config : false;