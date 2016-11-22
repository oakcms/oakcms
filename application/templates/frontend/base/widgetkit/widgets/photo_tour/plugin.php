<?php

return array(
    'name' => 'widget/photo_tour',
    'main' => '\\YOOtheme\\Widgetkit\\Widget\\Widget',
    'config' => array(
        'name'  => 'photo_tour',
        'label' => 'Photo Tour',
        'core'  => false,
        'icon'  => __DIR__.'/widget.svg',
        'view'  => __DIR__.'/views/view.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'class'              => 'slider'
        ),
        /*'fields' => array(
            array(
                'type' => 'text',
                'name' => 'status',
                'label' => Yii::t('widgets', 'Status')
            )
        ),*/
    ),

    'events' => array(
        'init.site' => function($event, $app) {
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('photo_tour.edit', __DIR__.'/views/edit.php', false);
        }
    )

);
