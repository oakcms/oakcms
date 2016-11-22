<?php

return array(

    'name' => 'widget/slideshow-panel',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'  => 'slideshow-panel',
        'label' => 'Slideshow Panel',
        'core'  => true,
        'icon'  => 'plugins/widgets/slideshow-panel/widget.svg',
        'view'  => 'plugins/widgets/slideshow-panel/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'panel'              => 'blank',
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
            'media_align'        => 'top',
            'media_width'        => '1-2',
            'media_breakpoint'   => 'medium',
            'content_align'      => true,

            'title'              => true,
            'content'            => true,
            'title_size'         => 'h3',
            'content_size'       => '',
            'text_align'        => 'left',
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

        'init.site' => function($event, $app) {
            $app['scripts']->add('uikit-slideshow', 'vendor/assets/uikit/js/components/slideshow.min.js', array('uikit'));
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('slideshow-panel.edit', 'plugins/widgets/slideshow-panel/views/edit.php', true);
        }

    )

);
