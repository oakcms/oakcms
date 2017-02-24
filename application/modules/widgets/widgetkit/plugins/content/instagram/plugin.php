<?php

return array(

  'name' => 'content/instagram',

  'main' => 'YOOtheme\\Widgetkit\\Content\\instagram\\InstagramType',

  'autoload' => array(
      'YOOtheme\\Widgetkit\\Content\\instagram\\' => 'src'
  ),

  'config' => array(

    'name' => 'instagram',
    'label' => 'Instagram',
    'icon' => 'plugins/content/instagram/content.svg',
    'item' => array('title', 'content', 'media', 'link'),
    'data' => array(
      'limit' => 10,
      'title' => 'username'
    )

  ),

  'items' => function ($items, $content, $app) {

        // determine api method and parameters
        $params = array('limit' => (int) $content['limit'], 'username' => $content['username'], 'title' => $content['title']);
        $posts = $app['instagram']->fetch($params, $content);

        foreach ($posts as $post) {
            $items->add($post);
        }
    },

  'events' => array(

    'init.admin' => function ($event, $app) {
      //$app['scripts']->add('widgetkit-instagram-controller', 'plugins/content/Instagram/assets/controller.js');
      $app['angular']->addTemplate('instagram.edit', 'plugins/content/instagram/views/edit.php');
    }

    )

  );
