<?php

return array(

    'name' => 'content/twitter',

    'main' => 'YOOtheme\\Widgetkit\\Content\\Twitter\\TwitterType',

    'autoload' => array(
        'YOOtheme\\Widgetkit\\Content\\Twitter\\' => 'src'
    ),

    'config' => array(

        'name' => 'twitter',
        'label' => 'Twitter',
        'icon' => 'plugins/content/twitter/content.svg',
        'item' => array('title', 'content', 'media', 'link'),
        'data' => array(
            'include_rts' => true,
            'include_replies' => false,
            'only_media' => false,
            'source' => 'user',
            'blacklist' => '',
            'limit' => 5,
            'title' => 'name'
        ),
        'credentials' => array(
            'consumer_key' => 'jtSjLhhoh5hFu86tRauqWfyv4',
            'consumer_secret' => 'uKiiyEm3fzcIK6rhL7A208ALaNx94QyBxgDq53SFAUdWc0zRYu'
        )

    ),

    'items' => function ($items, $content, $app) {

        $token = $app['option']->get('twitter_token', array());

        // determine api method and parameters
        $params = array('limit' => (int) $content['limit'], 'include_rts' => $content['include_rts']);

        if ($content['source'] == 'user') {
            $method = 'statuses/user_timeline';
            $params['screen_name'] = trim($content['search'], '@');
            $params['exclude_replies'] = !$content['include_replies'];
        } else {
            $method = 'search/tweets';
            $params['q'] = $content['search'];
        }

        // fetch tweets
        try {

            $tweets = $app['twitter']->fetch($method, $token, $params, $content);

            foreach ($tweets as $tweet) {
                $items->add($tweet);
            }

        } catch (\Exception $e) {

            $items->add(array('title' => 'Twitter Error', 'content' => 'Fetching tweets failed with message: ' . $e->getMessage()));
        }
    },

    'events' => array(

        'init.admin' => function ($event, $app) {
            $app['scripts']->add('widgetkit-twitter-controller', 'plugins/content/twitter/assets/controller.js');
            $app['angular']->addTemplate('twitter.edit', 'plugins/content/twitter/views/edit.php');
        }

    )

);
