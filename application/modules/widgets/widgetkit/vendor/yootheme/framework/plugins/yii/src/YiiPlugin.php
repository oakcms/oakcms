<?php

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
    /*public function main(Application $app)
    {
        jimport('joomla.filesystem.folder');
        jimport('joomla.application.component.helper');

        $app['db'] = function () {
            return new Database(JFactory::getDBO());
        };

        $app['url'] = function ($app) {
            return new UrlGenerator($app['request'], $app['locator']);
        };

        $app['request'] = function ($app) {

            $baseUrl   = rtrim(JURI::root(false), '/');
            $basePath  = rtrim(strtr(JPATH_ROOT, '\\', '/'), '/');
            $baseRoute = 'index.php';

            if (isset($app['component'])) {
                $baseRoute .= '?option='.$app['component'];
            }

            return new Request($baseUrl, $basePath, $baseRoute);
        };

        $app['response'] = function ($app) {
            return new ResponseProvider($app['url']);
        };

        $app['csrf'] = function () {
            return new CsrfProvider;
        };

        $app['users'] = function ($app) {
            return new UserProvider($app['component'], isset($app['permissions']) ? $app['permissions'] : array());
        };

        $app['date'] = function () {

            $date = new DateHelper();
            $date->setFormats(array(
                'full'   => JText::_('DATE_FORMAT_LC2'),
                'long'   => JText::_('DATE_FORMAT_LC3'),
                'medium' => JText::_('DATE_FORMAT_LC1'),
                'short'  => JText::_('DATE_FORMAT_LC4')
            ));

            return $date;
        };

        $app['locale'] = function($app) {
            return str_replace('-', '_', $app['joomla.language']->get('tag'));
        };

        $app['admin'] = function ($app) {
            return $app['joomla']->isAdmin();
        };

        $app['session'] = function () {
            return JFactory::getSession();
        };

        $app['joomla'] = function () {
            return JFactory::getApplication();
        };

        $app['joomla.config'] = function () {
            return JFactory::getConfig();
        };

        $app['joomla.language'] = function () {
            return JFactory::getLanguage();
        };

        $app['joomla.document'] = function () {
            return JFactory::getDocument();
        };

        $app['joomla.article'] = function () {
            return new ArticleHelper;
        };

        $app->extend('filter', function ($filter) {
            return $filter->register('content', new ContentFilter());
        });

        $app->on('boot', array($this, 'boot'));
        $app->on('view', array($this, 'registerAssets'), -10);
    }*/

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

            //$baseUrl  = ltrim(\Yii::$app->homeUrl, '/');
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

        $this->init();
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

                if($this['admin']) {
                    $view->registerCssFile(htmlentities($this['url']->to($source, array(), true)), [], $id);
                } else {

                    if($this['config']->get('disable_frontend_style')) {
                        if($id != 'wk-styles-css') {
                            $view->registerCssFile(htmlentities($this['url']->to($source, array(), true)), [], $id);
                        }
                    } else {
                        $view->registerCssFile(htmlentities($this['url']->to($source, array(), true)), [], $id);
                    }

                }

            } elseif ($content = $style->getContent()) {
                $view->registerCss($content);
            }
        }

        foreach ($this['scripts'] as $script) {
            if ($source = $script->getSource()) {
                $view->registerJsFile(htmlentities($this['url']->to($source, array(), true)), ['depends' => [\yii\web\JqueryAsset::className()], 'position' => View::POS_HEAD]);
            } elseif ($content = $script->getContent()) {
                $view->registerJs($content, View::POS_HEAD);
            }
        }

    }
}
