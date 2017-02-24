<?php

return array(

    'name' => 'widget/parallax',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'  => 'parallax',
        'label' => 'Parallax',
        'core'  => true,
        'icon'  => 'plugins/widgets/parallax/widget.svg',
        'view'  => 'plugins/widgets/parallax/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'fullscreen'               => false,
            'min_height'               => '300',
            'background_translatey'    => '-200',
            'background_color_start'   => '',
            'background_color_end'     => '',
            'contrast'                 => true,
            'media_query'              => '',
            'title_opacity_start'      => '1',
            'title_opacity_end'        => '',
            'title_translatex_start'   => '0',
            'title_translatex_end'     => '',
            'title_translatey_start'   => '0',
            'title_translatey_end'     => '',
            'title_scale_start'        => '1',
            'title_scale_end'          => '',
            'content_opacity_start'    => '1',
            'content_opacity_end'      => '',
            'content_translatex_start' => '0',
            'content_translatex_end'   => '',
            'content_translatey_start' => '0',
            'content_translatey_end'   => '',
            'content_scale_start'      => '1',
            'content_scale_end'        => '',
            'viewport'                 => '1',
            'velocity'                 => '0.5',
            'target'                   => false,

            'media'                    => true,
            'image_width'              => 'auto',
            'image_height'             => 'auto',

            'title'                    => true,
            'content'                  => true,
            'title_size'               => 'h1',
            'content_size'             => 'large',
            'text_align'               => 'center',
            'link'                     => true,
            'link_style'               => 'button',
            'link_text'                => 'Read more',
            'width'                    => '9-10',
            'width_small'              => '4-5',
            'width_medium'             => '2-3',
            'width_large'              => '1-2',

            'link_target'              => false,
            'class'                    => ''
        )

    ),

    'events' => array(

        'init.site' => function($event, $app) {
            $app['scripts']->add('uikit2-parallax', "vendor/assets/uikit/js/components/parallax.min.js", array('uikit2'));
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('parallax.edit', 'plugins/widgets/parallax/views/edit.php', true);
        }

    )

);
