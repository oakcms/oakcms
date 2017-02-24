<?php

return array(

    'name' => 'widget/lightslider',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'  => 'lightslider',
        'label' => Yii::t('widgets', 'Light Slider'),
        'core'  => true,
        'icon'  => 'plugins/widgets/lightslider/widget.svg',
        'view'  => 'plugins/widgets/lightslider/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'gallery'            => false,
            'nav'                => 'dotnav',
            'nav_overlay'        => true,
            'nav_align'          => 'center',
            'thumbnail_width'    => '70',
            'thumbnail_height'   => '70',
            'thumbnail_alt'      => false,
            'slidenav'           => 'default',
            'nav_contrast'       => true,
            'animation'          => 'fade',
            'slices'             => '15',
            'duration'           => '500',
            'autoplay'           => false,
            'interval'           => '3000',
            'autoplay_pause'     => true,
            'kenburns'           => false,
            'kenburns_animation' => '',
            'kenburns_duration'  => '15',
            'fullscreen'         => false,
            'min_height'         => '300',

            'media'              => true,
            'image_width'        => 'auto',
            'image_height'       => 'auto',
            'overlay'            => 'none',
            'overlay_animation'  => 'fade',
            'overlay_background' => true,

            'title'              => true,
            'content'            => true,
            'title_size'         => 'h3',
            'content_size'       => '',
            'link'               => true,
            'link_style'         => 'button',
            'link_text'          => 'Read more',
            'badge'              => true,
            'badge_style'        => 'badge',

            'link_target'        => false,
            'class'              => ''
        )

    ),

    'events' => array(

        'init.site' => function($event, $app) {},

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('lightslider.edit', 'plugins/widgets/lightslider/views/edit.php', true);
        }

    )

);
