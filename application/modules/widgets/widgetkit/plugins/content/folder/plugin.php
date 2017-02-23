<?php

return array(

    'name' => 'content/folder',

    'main' => 'YOOtheme\\Widgetkit\\Content\\Type',

    'config' => array(

        'name'  => 'folder',
        'label' => 'Folder',
        'icon'  => 'assets/images/content-placeholder.svg',
        'item'  => array('title', 'content', 'media', 'link'),
        'data'  => array(
            'folder' => defined('WPINC') ? 'wp-content/uploads/' : 'images/', // J or WP?
            'sort_by' => 'filename_asc',
            'strip_leading_numbers' => false,
            'strip_trailing_numbers' => false
        )
    ),

    'items' => function($items, $content, $app) {

        $extensions = array('jpg', 'jpeg', 'gif', 'png');

        // caching
        $now       = time();
        $expires   = 5 * 60;
        $hash      = function($content) {
            $fields = array($content['folder'],
                $content['sort_by'],
                $content['strip_leading_numbers'],
                $content['strip_trailing_numbers']);
            return md5(serialize($fields));
        };

        $newitems = array();

        // cache invalid?
        if(array_key_exists('hash', $content) // never cached
            || $now - $content['hashed'] > $expires // cached values too old
            || $hash($content) != $content['hash']) { // content settings have changed

            $folder = trim($content['folder'], '/');
            $pttrn  = '/\.('.implode('|', $extensions).')$/i';
            $dir    = dirname(dirname(dirname( $app['path'] ))); // TODO: cleaner? system agnostic?
            $sort   = explode('_', $content['sort_by'] ?: 'filename_asc');

            if (!$files = glob($dir.'/'.$folder.'/*')) {
                return;
            }

            if($sort[0] == 'date') {
                usort($files, function($a, $b) {
                    return filemtime($a) > filemtime($b);
                });
            }

            if (isset($sort[1]) && $sort[1] == 'desc') {
                $files = array_reverse($files);
            }

            foreach ($files as $img) {

                // extension filter
                if (!preg_match($pttrn, $img)) {
                    continue;
                }

                $data = array();

                $data['title'] = basename($img);
                $data['media'] = $folder.'/'.basename($img);

                // remove extension
                $data['title'] = preg_replace('/\.[^.]+$/', '', $data['title']);

                // remove leading number
                if($content['strip_leading_numbers']) {
                    $data['title'] = preg_replace('/^\d+\s?+/', '', $data['title']);
                }

                // remove trailing numbers
                if($content['strip_trailing_numbers']) {
                    $data['title'] = preg_replace('/\s?+\d+$/', '', $data['title']);
                }

				// replace underscores with space
				$data['title'] = trim(str_replace('_', ' ', $data['title']));

                $newitems[] = $data;
            }

            // write cache
            $content['prepared'] = json_encode($newitems);
            $content['hash']     = $hash($content);
            $content['hashed']   = $now;
            $app['content']->save($content->toArray());

        } else {

            // cache is valid
            $newitems = json_decode($content['prepared'], true);

        }

        if($content['sort_by'] == "random") {
            shuffle($newitems);
        }

        if(is_numeric($content['max_images'])){
            $newitems = array_slice($newitems, 0, $content['max_images']);
        }

        foreach ($newitems as $data) {
            $items->add($data);
        }

    },

    'events' => array(

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('folder.edit', 'plugins/content/folder/views/edit.php');
            $app['scripts']->add('widgetkit-folder-controller', 'plugins/content/folder/assets/controller.js');
        }

    )

);
