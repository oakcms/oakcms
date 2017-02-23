<?php

return array(

    'name' => 'content/custom',

    'main' => 'YOOtheme\\Widgetkit\\Content\\Type',

    'config' => array(

        'name'  => 'custom',
        'label' => 'Custom',
        'icon'  => 'assets/images/content-placeholder.svg',
        'item'  => array('title', 'content', 'media'),
        'fields' => array(
            'email'       => array('type' => 'text', 'label' => 'Email', 'options' => array('icon' => 'envelope-o', 'attributes'=>array('placeholder'=>'your@email.com'))),
            'facebook'    => array('type' => 'text', 'label' => 'Facebook', 'options' => array('icon' => 'facebook', 'attributes'=>array('placeholder'=>'http://'))),
            'badge'       => array('type' => 'text', 'label' => 'Badge', 'options' => array('icon' => 'bookmark-o', 'attributes'=>array('placeholder'=>''))),
            'google-plus' => array('type' => 'text', 'label' => 'Google Plus','options' => array('icon' => 'google-plus', 'attributes'=>array('placeholder'=>'http://'))),
            'location'    => array('type' => 'location', 'label' => 'Location'),
            'tags'        => array('type' => 'tags', 'label' => 'Tags'),
            'media2'      => array('type' => 'media', 'label' => 'Media 2'),
            'twitter'     => array('type' => 'text', 'label' => 'Twitter', 'options' => array( 'icon' => 'twitter', 'attributes'=>array('placeholder'=>'http://'))),
            'date'        => array('type' => 'date', 'label' => 'Date')
        ),
        'data'  => array(
            'items' => array(),
            'random' => 0,
            'parse_shortcodes' => 1
        )

    ),

    'items' => function($items, $content, $app) {
        if (is_array($content['items'])) {

            if ($content['random']) {
                $newitems = $content['items'];
                shuffle($newitems);
                $content['items'] = $newitems;
            }

            if (!isset($content['parse_shortcodes'])) {
                $content['parse_shortcodes'] = 1;
            }

            foreach ($content['items'] as $data) {
                if (isset($data['content']) && $content['parse_shortcodes']) {
                    $data['content'] = $app['filter']->apply($data['content'], 'content');
                }
                $items->add($data);
            }
        }

    },

    'events' => array(

        'init.admin' => function($event, $app) {

            if ($app['config']->get('system_editor')) {
                $app['scripts']->add('editor', 'WK_SYSTEM_EDITOR = "'.$app['config']->get('system_editor').'";', array(), 'string');
            }

            $app['scripts']->add('widgetkit-custom-controller', 'plugins/content/custom/assets/controller.js');
            $app['angular']->addTemplate('custom.edit', 'plugins/content/custom/views/edit.php', true);
        }

    )

);
