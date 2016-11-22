<?php

defined('_JEXEC') or die;

if ($component = JComponentHelper::getComponent('com_widgetkit', true) and $component->enabled) {
    return include(__DIR__ . '/widgetkit.php');
}

return false;