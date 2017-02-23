<?php

namespace YOOtheme\Framework\Joomla;

use JAdministratorHelper, JComponentHelper, JDocument, JFactory, JFolder, JURI, JRequest, JText;
use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;
use YOOtheme\Framework\Plugin\Plugin;
use YOOtheme\Framework\Routing\JsonResponse;
use YOOtheme\Framework\Routing\RawResponse;
use YOOtheme\Framework\Routing\Request;
use YOOtheme\Framework\Routing\ResponseProvider;

class JoomlaPlugin extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function main(Application $app)
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
    }

    /**
     * Callback for 'boot' event.
     */
    public function boot($event, $app)
    {
        if (!is_dir($app['path.cache']) && !JFolder::create($app['path.cache'])) {
            throw new \RuntimeException(sprintf('Unable to create cache folder in "%s"', $app['path.cache']));
        }

        if (isset($app['component'])) {
            $this->registerComponent($app);
        }

        $app['joomla']->registerEvent('onAfterRoute', array($this, 'init'));

        // using onBeforeCompileHead as onBeforeRender is triggered too early on some circumstances
        $app['joomla']->registerEvent('onBeforeCompileHead', function () use ($app) {
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
        foreach ($this['styles'] as $style) {

            $id = sprintf('%s-css', $style->getName());

            if ($source = $style->getSource()) {
                $this['joomla.document']->addStyleSheet(htmlentities($this['url']->to($source)), 'text/css', null, compact('id'));
            } elseif ($content = $style->getContent()) {
                $this['joomla.document']->addStyleDeclaration($content);
            }
        }

        foreach ($this['scripts'] as $script) {
            if ($source = $script->getSource()) {
                $this['joomla.document']->addScript(htmlentities($this['url']->to($source)));
            } elseif ($content = $script->getContent()) {
                $this['joomla.document']->addScriptDeclaration($content);
            } elseif ($template = $script->getOption('template')) {
                $this['joomla.document']->addCustomTag(sprintf("<script id=\"%s\" type=\"text/template\">%s</script>\n", $script->getName(), $this['view']->render($template)));
            }
        }
    }

    /**
     * Registers Joomla component integration.
     */
    protected function registerComponent(Application $app)
    {
        $app['joomla']->registerEvent('onAfterDispatch', function () use ($app) {

            if ($app['component'] !== ($app['admin'] ? JAdministratorHelper::findOption() : $app['joomla']->input->get('option'))) {
                return;
            }

            $response = $app->handle(null, false);

            if ($response->getStatus() != 200) {
                $app['joomla']->setHeader('status', $response->getStatus());
            }

            if ($response instanceof JsonResponse) {
                JRequest::setVar('format', 'json');
                $app['joomla']->loadDocument(JDocument::getInstance('json')->setBuffer((string) $response));
            } elseif ($response instanceof RawResponse) {
                JRequest::setVar('format', 'raw');
                $app['joomla']->loadDocument(JDocument::getInstance('raw')->setBuffer((string) $response));
            } else {
                $app['joomla.document']->setBuffer((string) $response, 'component');
            }

        });
    }
}
