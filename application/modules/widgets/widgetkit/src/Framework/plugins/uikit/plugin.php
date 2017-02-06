<?php

return array(

    'name' => 'framework/uikit',

    'main' => function($app) {

        $app->on('boot', function($event, $app) {

            $path = sprintf('%s/assets/uikit/js', $app['path.framework']);

            $app['scripts']->register('uikit2', $path.'/uikit.min.js');
            $app['scripts']->register('uikit2-accordion', $path.'/components/accordion.min.js', 'uikit2');
            $app['scripts']->register('uikit2-autocomplete', $path.'/components/autocomplete.min.js', 'uikit2');
            $app['scripts']->register('uikit2-datepicker', $path.'/components/datepicker.min.js', 'uikit2');
            $app['scripts']->register('uikit2-form-password', $path.'/components/form-password.min.js', 'uikit2');
            $app['scripts']->register('uikit2-form-select', $path.'/components/form-select.min.js', 'uikit2');
            $app['scripts']->register('uikit2-htmleditor', $path.'/components/htmleditor.min.js', 'uikit2');
            $app['scripts']->register('uikit2-lightbox', $path.'/components/lightbox.min.js', 'uikit2');
            $app['scripts']->register('uikit2-nestable', $path.'/components/nestable.min.js', 'uikit2');
            $app['scripts']->register('uikit2-notify', $path.'/components/notify.min.js', 'uikit2');
            $app['scripts']->register('uikit2-pagination', $path.'/components/pagination.min.js', 'uikit2');
            $app['scripts']->register('uikit2-search', $path.'/components/search.min.js', 'uikit2');
            $app['scripts']->register('uikit2-slideshow-fx', $path.'/components/slideshow-fx.min.js', 'uikit2');
            $app['scripts']->register('uikit2-slideshow', $path.'/components/slideshow.min.js', 'uikit2');
            $app['scripts']->register('uikit2-sortable', $path.'/components/sortable.min.js', 'uikit2');
            $app['scripts']->register('uikit2-sticky', $path.'/components/sticky.min.js', 'uikit2');
            $app['scripts']->register('uikit2-timepicker', $path.'/components/timepicker.min.js', 'uikit2');
            $app['scripts']->register('uikit2-tooltip', $path.'/components/tooltip.min.js', 'uikit2');
            $app['scripts']->register('uikit2-upload', $path.'/components/upload.min.js', 'uikit2');

        }, 10);

    }

);
