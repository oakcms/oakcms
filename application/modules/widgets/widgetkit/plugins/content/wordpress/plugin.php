<?php

$config = array(

    'name' => 'content/wordpress',

    'main' => 'YOOtheme\\Widgetkit\\Content\\Type',

    'config' => function($app) {

        return array(

            'name'  => 'wordpress',
            'label' => 'WordPress',
            'icon'  => $app['url']->to('plugins/content/wordpress/content.svg'),
            'item'  => array('title', 'content', 'media', 'link'),
            'data'  => array(
                'number'   => 5,
                'content'  => 'intro',
                'category' => array(),
                'order_by' => 'post_date'
            )

        );
    },

    'items' => function($items, $content) {

        $order = explode('_asc', $content['order_by']);
        $args  = array(
            'numberposts' => $content['number'] ?: 5,
            'category'    => $content['category'] ? implode(',', $content['category']) : 0,
            'orderby'     => isset($order[0]) ? $order[0] : 'post_date',
            'order'       => isset($order[1]) ? 'ASC' : 'DESC',
            'post_status' => 'publish'
        );

        foreach (get_posts($args) as $post) {

            $data = array();
            $data['title'] = get_the_title($post->ID);
            $data['link']  = get_permalink($post->ID);

            $pieces = get_extended($post->post_content);

            if ($content['content'] == 'excerpt') {
                $data['content'] = apply_filters('the_content', $post->post_excerpt);
            } else if ($content['content'] == 'intro') {
                $data['content'] = apply_filters('the_content', $pieces['main']);
            } else {
                $data['content'] = apply_filters('the_content', $pieces['main'].$pieces['extended']);
            }

            if ($thumbnail = get_post_thumbnail_id($post->ID)) {
                $image = wp_get_attachment_image_src($thumbnail, 'full');
                $data['media'] = $image[0];
            }

            $items->add($data);
        }

    },

    'events' => array(

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('wordpress.edit', 'plugins/content/wordpress/views/edit.php');
        }

    )

);

return defined('WPINC') ? $config : false;
