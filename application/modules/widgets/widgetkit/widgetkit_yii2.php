<?php

global $widgetkit;

if ($widgetkit) {
    return $widgetkit;
}

$loader = require __DIR__.'/vendor/autoload.php';
$config = require __DIR__.'/config.php';

$app = new YOOtheme\Widgetkit\Application($config);
$app['autoloader'] = $loader;

$app['templates'] = function() {
    return file_exists($dir = Yii::getAlias('@app').'/templates/frontend/' . Yii::$app->keyStorage->get('themeFrontend').'/widgetkit') ? array($dir) : array();
};

$app->on('init', function ($event, $app) {
    $app['config']->add(\Yii::$app->getModule('admin')->getSettings('widgets'));

    if ($app['admin'] && strpos(Yii::$app->request->pathInfo, 'admin') !== false) {
        $app->trigger('init.admin', array($app));
    }
});



$app->on('init.admin', function ($event, $app) {
    $app['angular']->addTemplate('media', 'views/media.php', true);
    $app['styles']->add('widgetkit-yii2', 'assets/css/yii2.css');
    $app['scripts']->add('widgetkit-yii2', 'assets/js/yii2.js', array('widgetkit-application'));
    $app['scripts']->add('widgetkit-yii2-media', 'assets/js/yii2.media.js', array('widgetkit-yii2'));
    $app['scripts']->add('uikit-upload');
    $app['config']->set('settings-page', \yii\helpers\Url::to(['/admin/modules/setting', 'name' => 'widgets']));

}, 10);

$app->on('view', function ($event, $app) {
    //$app['config']->set('theme.support', []);
});

$app->boot();
return $widgetkit = $app;
