<?php

$config = array(

    'name' => 'content/woocommerce',

    'main' => 'YOOtheme\\Widgetkit\\Content\\Type',

    'config' => function($app) {

        return array(

            'name'  => 'woocommerce',
            'label' => 'WooCommerce',
            'icon'  => $app['url']->to('plugins/content/woocommerce/content.svg'),
            'item'  => array('title', 'content', 'media', 'link'),
            'data'  => array(
                'number'   => 5,
                'content'  => 'intro',
                'category' => '',
                'order_by' => 'post_date'
            )

        );
    },

    'items' => function($items, $content) {

        $order = explode('_asc', $content['order_by']);
        $args  = array(
            'numberposts' => $content['number'] ?: 5,
            'orderby'     => isset($order[0]) ? $order[0] : 'post_date',
            'order'       => isset($order[1]) ? 'ASC' : 'DESC',
            'post_status' => 'publish',
            'post_type'   => 'product'
        );

        if ($content['category'] > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy'         => 'product_cat',
                    'field'            => 'id',
                    'terms'            => (int) $content['category'],
                    'include_children' => false
                )
            );
        }

        foreach (get_posts($args) as $post) {

            $data = array();
            $data['title'] = get_the_title($post->ID);
            $data['link'] = get_permalink($post->ID);

            $pieces = get_extended($post->post_content);

            if ($content['content'] == 'excerpt') {
                $data['content'] = apply_filters('the_content', $post->post_excerpt);
            } else if ($content['content'] == 'intro') {
                $data['content'] = apply_filters('the_content', $pieces['main']);
            } else {
                $data['content'] = apply_filters('the_content', $pieces['main'].$pieces['extended']);
            }

            // media: TODO
            if ($thumbnail = get_post_thumbnail_id($post->ID)) {
                $image = wp_get_attachment_image_src($thumbnail, 'full');
                $data['media'] = $image[0];
            }

            $items->add($data);
        }

    },

    'events' => array(

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('woocommerce.edit', 'plugins/content/woocommerce/views/edit.php');
        }

    )

);

return defined('WPINC') && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ? $config : false;