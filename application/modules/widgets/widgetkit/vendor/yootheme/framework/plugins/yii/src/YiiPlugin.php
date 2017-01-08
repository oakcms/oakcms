<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace YOOtheme\Framework\Yii;

use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\View;
use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;
use YOOtheme\Framework\Event\Event;
use YOOtheme\Framework\Plugin\PluginInterface;
use YOOtheme\Framework\Routing\Request;
use YOOtheme\Framework\Routing\ResponseProvider;

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

        //$this->init();
        $app->trigger('init', array($app));

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
                $id = sprintf('%s-css', $style->getName());
                $view->registerCssFile(htmlentities($this['url']->to($source, array(), true)), [], $id);
            } elseif ($content = $style->getContent()) {
                $view->registerCss($content);
            }
        }

        foreach ($this['scripts'] as $script) {
            if ($source = $script->getSource()) {
                $view->registerJsFile(htmlentities($this['url']->to($source, array(), true)), ['depends' => ['\yii\web\JqueryAsset'], 'position' => View::POS_HEAD]);
            } elseif ($content = $script->getContent()) {
                $view->registerJs($content, View::POS_HEAD);
            }
        }
    }
}
