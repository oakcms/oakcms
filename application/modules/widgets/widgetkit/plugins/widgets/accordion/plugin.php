<?php

return array(

    'name' => 'widget/accordion',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'  => 'accordion',
        'label' => 'Accordion',
        'core'  => true,
        'icon'  => 'plugins/widgets/accordion/widget.svg',
        'view'  => 'plugins/widgets/accordion/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'collapse'          => true,
            'first_item'        => true,

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
            'title_size'        => 'h3',
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
            $app['scripts']->add('uikit-accordion', 'vendor/assets/uikit/js/components/accordion.min.js', array('uikit'));
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('accordion.edit', 'plugins/widgets/accordion/views/edit.php', true);
        }

    )

);
