<?php

return array(

    'name' => 'widget/slider',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'  => 'slider',
        'label' => 'Slider',
        'core'  => true,
        'icon'  => 'plugins/widgets/slider/widget.svg',
        'view'  => 'plugins/widgets/slider/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'slidenav'                 => 'default',
            'slidenav_contrast'        => true,
            'infinite'                 => true,
            'center'                   => false,
            'autoplay'                 => false,
            'interval'                 => '3000',
            'autoplay_pause'           => true,
            'gutter'                   => 'default',
            'columns'                  => '1',
            'columns_small'            => 0,
            'columns_medium'           => 0,
            'columns_large'            => 0,
            'columns_xlarge'           => 0,
            'fullscreen'               => false,
            'min_height'               => '300',

            'media'                    => true,
            'image_width'              => 'auto',
            'image_height'             => 'auto',
            'overlay_hover'            => true,
            'overlay_background'       => 'hover',
            'overlay_animation'        => 'fade',
            'overlay_image'            => 'static',
            'image_animation'          => 'fade',
            'overlay_link'             => false,

            'title'                    => true,
            'content'                  => true,
            'title_size'               => 'h3',
            'content_size'             => '',
            'text_align'               => 'center',
            'link'                     => true,
            'link_style'               => 'button',
            'link_text'                => 'Read more',

            'link_target'              => false,
            'class'                    => ''
        )

    ),

    'events' => array(

        'init.site' => function($event, $app) {
            $app['scripts']->add('uikit-slider', 'vendor/assets/uikit/js/components/slider.min.js', array('uikit'));
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('slider.edit', 'plugins/widgets/slider/views/edit.php', true);
        }

    )

);
