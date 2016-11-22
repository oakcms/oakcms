<?php

use YOOtheme\Framework\View\View;
use YOOtheme\Framework\View\Asset\AssetFactory;
use YOOtheme\Framework\View\Asset\AssetManager;
use YOOtheme\Framework\View\Asset\Filter\CssImageBase64Filter;
use YOOtheme\Framework\View\Asset\Filter\CssImportResolverFilter;
use YOOtheme\Framework\View\Asset\Filter\CssRewriteUrlFilter;
use YOOtheme\Framework\View\Asset\Filter\CssRtlFilter;
use YOOtheme\Framework\View\Asset\Filter\FilterManager;
use YOOtheme\Framework\View\Helper\AttributeHelper;
use YOOtheme\Framework\View\Helper\MacroHelper;
use YOOtheme\Framework\View\Loader\ResourceLoader;

return array(

    'name' => 'framework/view',

    'main' => function ($app, $self) {

        $app['view'] = function ($app) {

            $helpers = array(
                'attrs' => new AttributeHelper,
                'macro' => new MacroHelper
            );

            $view = new View($app['view.loader']);
            $view->set('app', $app);
            $view->set('view', $view);
            $view->addHelpers($helpers);

            return $view;
        };

        $app['view.loader'] = function ($app) {
            return new ResourceLoader($app['locator']);
        };

        $app['assets'] = function ($app) {
            return new AssetFactory($app['view.loader']);
        };

        $app['styles'] = function ($app) {
            return new AssetManager($app['assets'], $app['filters'], $app['styles.cache']);
        };

        $app['styles.cache'] = function ($app) {
            return isset($app['path.cache']) ? $app['path.cache'] . '/%name%.css' : null;
        };

        $app['scripts'] = function ($app) {
            return new AssetManager($app['assets'], $app['filters'], $app['scripts.cache']);
        };

        $app['scripts.cache'] = function ($app) {
            return isset($app['path.cache']) ? $app['path.cache'] . '/%name%.js' : null;
        };

        $app['filters'] = function ($app) {
            return new FilterManager(array(
                'CssImageBase64' => new CssImageBase64Filter($app['request']),
                'CssImportResolver' => new CssImportResolverFilter,
                'CssRewriteUrl' => new CssRewriteUrlFilter($app['url']),
                'CssRtl' => new CssRtlFilter,
            ));
        };

        $app->on('boot', function ($event, $app) {

            $path = sprintf('%s/yootheme/framework/', $app['path.vendor']);

            $app['scripts']->register('vue', $path . 'assets/vue/dist/vue.min.js');
            $app['scripts']->register('vue-config', $path . 'plugins/view/lib/vue-config.min.js', array('vue', 'config'));
            $app['scripts']->register('vue-resource', $path . 'assets/vue-resource/dist/vue-resource.min.js', 'vue-config');
            $app['scripts']->register('vue-translator', $path . 'plugins/view/lib/vue-translator.min.js', 'vue-config');

        }, 10);

        $app->on('view', function ($event, $app) use ($self) {

            $config = array_merge(array(
                'url' => $app['url']->base(),
                'route' => $app['url']->route(),
                'locale' => $app['locale'],
                'locales' => $app['translator']->getResources()
            ), $self->config);

            if (isset($app['csrf'])) {
                $config['csrf'] = $app['csrf']->generate();
            }

            if (isset($app['path'])) {
                $config['base'] = $app['url']->to($app['path']);
            }

            $app['scripts']->register('config', sprintf('var $config = %s;', json_encode($config)), array(), 'string');

        }, 10);

    }

);
