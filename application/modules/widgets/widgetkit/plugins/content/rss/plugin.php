<?php

return array(

    'name' => 'content/rss',

    'main' => 'YOOtheme\\Widgetkit\\Content\\rss\\RSSType',

    'autoload' => array(
        'YOOtheme\\Widgetkit\\Content\\rss\\' => 'src'
    ),

    'config' => array(

        'name'  => 'rssfeed',
        'label' => 'RSSFeed',
        'icon'  => 'plugins/content/rss/content.svg',
        'item'  => array('title', 'content', 'link', 'date'),
        'data'  => array(
            'limit' => 10,
            'src' => ''
        )
    ),

    'items' => function ($items, $content, $app) {

        // determine api method and parameters
        $params = array('limit' => (int) $content['limit'], 'source' => $content['src']);
        $posts  = $app['rss']->fetch($params, $content);

        foreach ($posts as $post) {
            $items->add($post);
        }
    },

    'events' => array(

        'init.admin' => function ($event, $app) {
            $app['angular']->addTemplate('rssfeed.edit', 'plugins/content/rss/views/edit.php');
        }
    )
  );
