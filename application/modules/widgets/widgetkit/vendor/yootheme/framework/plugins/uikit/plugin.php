<?php

return array(

    'name' => 'framework/uikit',

    'main' => function($app) {

        $app->on('boot', function($event, $app) {

            $path = sprintf('%s/yootheme/framework/assets/uikit/js', $app['path.vendor']);

            $app['scripts']->register('uikit', $path.'/uikit.min.js');
            $app['scripts']->register('uikit-accordion', $path.'/components/accordion.min.js', 'uikit');
            $app['scripts']->register('uikit-autocomplete', $path.'/components/autocomplete.min.js', 'uikit');
            $app['scripts']->register('uikit-datepicker', $path.'/components/datepicker.min.js', 'uikit');
            $app['scripts']->register('uikit-form-password', $path.'/components/form-password.min.js', 'uikit');
            $app['scripts']->register('uikit-form-select', $path.'/components/form-select.min.js', 'uikit');
            $app['scripts']->register('uikit-htmleditor', $path.'/components/htmleditor.min.js', 'uikit');
            $app['scripts']->register('uikit-lightbox', $path.'/components/lightbox.min.js', 'uikit');
            $app['scripts']->register('uikit-nestable', $path.'/components/nestable.min.js', 'uikit');
            $app['scripts']->register('uikit-notify', $path.'/components/notify.min.js', 'uikit');
            $app['scripts']->register('uikit-pagination', $path.'/components/pagination.min.js', 'uikit');
            $app['scripts']->register('uikit-search', $path.'/components/search.min.js', 'uikit');
            $app['scripts']->register('uikit-slideshow-fx', $path.'/components/slideshow-fx.min.js', 'uikit');
            $app['scripts']->register('uikit-slideshow', $path.'/components/slideshow.min.js', 'uikit');
            $app['scripts']->register('uikit-sortable', $path.'/components/sortable.min.js', 'uikit');
            $app['scripts']->register('uikit-sticky', $path.'/components/sticky.min.js', 'uikit');
            $app['scripts']->register('uikit-timepicker', $path.'/components/timepicker.min.js', 'uikit');
            $app['scripts']->register('uikit-tooltip', $path.'/components/tooltip.min.js', 'uikit');
            $app['scripts']->register('uikit-upload', $path.'/components/upload.min.js', 'uikit');

        }, 10);

    }

);
