<?php

return array(

    'name' => 'widget/switcher',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'  => 'switcher',
        'label' => 'Switcher',
        'core'  => true,
        'icon'  => 'plugins/widgets/switcher/widget.svg',
        'view'  => 'plugins/widgets/switcher/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'nav'               => 'nav',
            'thumbnail_width'   => '70',
            'thumbnail_height'  => '70',
            'thumbnail_alt'     => false,
            'position'          => 'top',
            'alignment'         => 'left',
            'width'             => '1-4',
            'panel'             => false,
            'animation'         => 'none',

            'media'             => true,
            'image_width'       => 'auto',
            'image_height'      => 'auto',
            'media_align'       => 'top',
            'media_width'       => '1-2',
            'media_breakpoint'  => 'medium',
            'content_align'     => true,
            'media_border'      => 'none',
            'media_overlay'     => 'icon',
            'overlay_animation' => 'fade',
            'media_animation'   => 'scale',

            'title'             => true,
            'content'           => true,
            'social_buttons'    => true,
            'title_size'        => 'panel',
            'text_align'        => 'left',
            'link'              => true,
            'link_style'        => 'button',
            'link_text'         => 'Read more',

            'link_target'       => false,
            'class'             => ''
        )

    ),

    'events' => array(

        'init.site' => function($event, $app) {
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('switcher.edit', 'plugins/widgets/switcher/views/edit.php', true);
        }

    )

);
