<?php

return array(

    'name' => 'widget/list',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

    'config' => array(

        'name'  => 'list',
        'label' => 'List',
        'core'  => true,
        'icon'  => 'plugins/widgets/list/widget.svg',
        'view'  => 'plugins/widgets/list/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'list'              => 'line',

            'media'             => true,
            'image_width'       => 'auto',
            'image_height'      => 'auto',
            'media_align'       => 'left',
            'content_align'     => true,
            'media_border'      => 'none',

            'title'             => 'title',
            'title_size'        => 'default',
            'title_truncate'    => '',
            'link'              => true,
            'link_color'        => 'muted',

            'link_target'       => false,
            'class'             => ''
        )

    ),

    'events' => array(

        'init.site' => function($event, $app) {
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('list.edit', 'plugins/widgets/list/views/edit.php', true);
        }

    )

);
