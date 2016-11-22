<?php

return array(

    'name' => 'widget/switcher-panel',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'  => 'switcher-panel',
        'label' => 'Switcher Panel',
        'core'  => true,
        'icon'  => 'plugins/widgets/switcher-panel/widget.svg',
        'view'  => 'plugins/widgets/switcher-panel/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'panel'             => 'blank',
            'image'             => '',
            'image_hero_width'  => 'auto',
            'image_hero_height' => 'auto',
            'image_min_height'  => '200',

            'nav'               => 'nav',
            'thumbnail_width'   => '70',
            'thumbnail_height'  => '70',
            'thumbnail_alt'     => false,
            'alignment'         => 'left',
            'contrast'          => true,

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
            $app['angular']->addTemplate('switcher-panel.edit', 'plugins/widgets/switcher-panel/views/edit.php', true);
        }

    )

);
