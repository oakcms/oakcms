<?php

use YOOtheme\Widgetkit\Application;
use YOOtheme\Widgetkit\Framework\Joomla\Option;

global $widgetkit;

if ($widgetkit) {
    return $widgetkit;
}

$loader = require __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config.php';

$app = new Application($config);
$app['autoloader']  = $loader;
$app['path.cache']  = rtrim(JPATH_SITE, '/').'/media/widgetkit';
$app['component']   = 'com_'.$app['name'];
$app['permissions'] = array('core.manage' => 'manage_widgetkit');
$app['templates']   = function () {
    $db = JFactory::getDbo();
    $db->setQuery( 'SELECT id,template FROM #__template_styles WHERE client_id=0 AND home=1');
    $template = $db->loadObject()->template;

    return file_exists($path = rtrim(JPATH_ROOT, '/')."/templates/".$template."/widgetkit") ? array($path) : array();
};
$app['option'] = function ($app) {
    return new Option($app['db'], 'pkg_widgetkit');
};

$app->on('init', function ($event, $app) {

    $controller = JFactory::getApplication()->input->get('controller');
    $option = JFactory::getApplication()->input->get('option');

    if ($option == 'com_config' && $controller == 'config.display.modules') {
        $app['scripts']->add('widgetkit-joomla', 'assets/js/joomla.js', array('widgetkit-application'));
    }

    $app['config']->add(JComponentHelper::getParams($app['component'])->toArray());

    // check theme support for widgetkit
    $template = JFactory::getApplication()->getTemplate(true);
    $warp_path = sprintf('%s/%s/warp.php', JPATH_THEMES, $template->template);
    $yoopro_path = sprintf('%s/%s/vendor/yootheme/theme/builder', JPATH_THEMES, $template->template);

    if (file_exists($warp_path)) {
        $app['config']->set('theme.support', true);
    } elseif (file_exists($yoopro_path)) {
        $app['config']->set('theme.support', 'noconflict');
    } else {
        $app['config']->set('theme.support', $template->params->get('widgetkit'));
    }

    if ($app['admin'] && $app['component'] == JAdministratorHelper::findOption()) {
        $app->trigger('init.admin', array($app));
    }

});

$app->on('init.admin', function ($event, $app) {
    JHtmlBehavior::keepalive();
    JHtml::_('jquery.framework');

    $app['angular']->addTemplate('media', 'views/media.php', true);
    $app['angular']->set('token', JSession::getFormToken());

    $app['styles']->add('widgetkit-joomla', 'assets/css/joomla.css');
    $app['scripts']->add('widgetkit-joomla', 'assets/js/joomla.js', array('widgetkit-application'));
    $app['scripts']->add('widgetkit-joomla-media', 'assets/js/joomla.media.js', array('widgetkit-joomla'));
    $app['scripts']->add('uikit2-upload');

    $app['config']->set('settings-page', 'index.php?option=com_config&view=component&component=com_widgetkit');

    // load JEditor
    if (in_array($editor = JFactory::getConfig()->get('editor'), array('tinymce', 'jce'))) {

        $app['scripts']->add('widgetkit-joomla-tinymce', 'assets/js/joomla.tinymce.js', array('widgetkit-joomla'));

        JHTML::_('behavior.modal');

        $editor = JEditor::getInstance($editor);
        $editor->display('wk_dummy_editor', '', '', '', '', '', false);
    }

}, 10);

$app->on('view', function ($event, $app) {
    JHtml::_('jquery.framework');
});

$app->boot();

return $widgetkit = $app;
