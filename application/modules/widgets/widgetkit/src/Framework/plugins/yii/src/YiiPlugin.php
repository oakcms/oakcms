<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace YOOtheme\Widgetkit\Framework\Yii;

use yii\helpers\Url;
use yii\web\View;
use YOOtheme\Widgetkit\Framework\Application;
use YOOtheme\Widgetkit\Framework\ApplicationAware;
use YOOtheme\Widgetkit\Framework\Event\Event;
use YOOtheme\Widgetkit\Framework\Plugin\PluginInterface;
use YOOtheme\Widgetkit\Framework\Routing\Request;
use YOOtheme\Widgetkit\Framework\Routing\ResponseProvider;

class YiiPlugin extends ApplicationAware implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function main(Application $app)
    {
        $app['db'] = function() {
            return new Database(\Yii::$app->db);
        };

        $app['url'] = function($app) {
            return new UrlGenerator($app['request'], $app['locator'], $app['name']);
        };

        $app['request'] = function() {
            $baseUrl  = rtrim(Url::home(true), '/');
            $basePath = rtrim(strtr(\Yii::getAlias('@webroot'), '\\', '/'), '/');

            $request  = function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ? array_map('stripslashes_deep', $_REQUEST) : $_REQUEST;

            return new Request($baseUrl, $basePath, $request);
        };

        $app['response'] = function($app) {
            return new ResponseProvider($app['url']);
        };

        $app['csrf'] = function() {
            return new CsrfProvider;
        };

        $app['locale'] = function() {
            return str_replace('-', '_', \Yii::$app->language);
        };

        $app['users'] = function() {
            return new UserProvider;
        };

        $app['config'] = function () {
            return new Config();
        };

        $app['option'] = function($app) {
            return new Option($app['name'] . '-');
        };

        $app['yii'] = function() {
            return \Yii::$app->view;
        };

        $app['admin'] = function($app) {
            return $app['yii']->isAdmin();
        };

        $app['update'] = function() {
            return new Update;
        };

        $app->extend('filter', function ($filter) {
            return $filter->register('content', new ContentFilter());
        });

        $app->on('boot', array($this, 'boot'));
        $app->on('view', array($this, 'registerAssets'), -10);
    }

    /**
     * Callback for 'boot' event.
     */
    public function boot(Event $event, Application $app)
    {
        if (!is_dir($app['path.cache']) && !mkdir($app['path.cache'], 0777, true)) {
            throw new \RuntimeException(sprintf('Unable to create cache folder in "%s"', $app['path.cache']));
        }

        $app->trigger('init', array($app));

        $this->init();

        $app['yii']->on(\yii\web\View::EVENT_BEGIN_BODY, function () use ($app) {
            $app->trigger('view', array($app));
        });
    }

    /**
     * Callback to initialize app.
     */
    public function init()
    {
        $this['plugins']->load();
        $this->app->trigger('init', array($this->app));
    }

    /**
     * Callback to register assets.
     */
    public function registerAssets()
    {

        $view = \Yii::$app->view;
        foreach ($this['styles'] as $style) {
            if ($source = $style->getSource()) {
                //$id = sprintf('%s-css', $style->getName());
                $view->registerCssFile(htmlentities($this['url']->to($source, array(), true)), []);
            } elseif ($content = $style->getContent()) {
                $view->registerCss($content);
            }
        }

        foreach ($this['scripts'] as $script) {
            if ($source = $script->getSource()) {
                $view->registerJsFile(htmlentities($this['url']->to($source, array(), true)), ['depends' => ['yii\web\JqueryAsset']]);
            } elseif ($content = $script->getContent()) {
                $view->registerJs($content, View::POS_HEAD, $script->getName());
            }
        }
    }
}
