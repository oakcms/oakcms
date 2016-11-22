<?php

namespace YOOtheme\Widgetkit\Content\rss;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;


class RSSApp extends ApplicationAware
{
    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function fetch($params, $content)
    {
        // Cache settings
        $now = time();
        $expires = 5 * 60;

        // Cache invalid?
        if (array_key_exists('hash', $content) // never cached
            || $now - $content['hashed'] > $expires // cached values too old
            || md5(serialize($params)) != $content['hash']) // content settings have changed
        {
            $feed = simplexml_load_string($this->url_get_contents($params['source']));
            $posts = array();

            if ($feed && isset($feed->channel->item)) {

                foreach ($feed->channel->item as $item) {

                    $posts[] = array(
                        'title'   => "".$item->title,
                        'content' => "".$item->description,
                        'date'    => "".$item->pubDate,
                        'link'    => "".$item->link
                    );

                    if ($params['limit'] && (sizeof($posts) == $params['limit']) || sizeof($posts) == 60 ) break;
                }

                // write cache
                $content['prepared'] = json_encode($posts);
                $content['hash'] = md5(serialize($params));
                $content['hashed'] = $now;

                $this->app['content']->save($content->toArray());

                return $posts;
            }

            if (!isset($content['prepared']) && md5(serialize($params)) != $content['hash']) {
                return $posts;
            }
        }

        // read from cache
        $posts = json_decode($content['prepared'], true);

        return $posts ? $posts : array();
    }

    protected function url_get_contents ($url) {

        $content = '';

        if (function_exists('curl_exec') && ini_get('open_basedir') === '') {
            $conn = curl_init($url);
            curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($conn, CURLOPT_FRESH_CONNECT,  true);
            curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($conn,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
            curl_setopt($conn, CURLOPT_AUTOREFERER, true);
            curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($conn, CURLOPT_VERBOSE, 0);

            $content = (curl_exec($conn));
            curl_close($conn);
        }

        if (!$content && function_exists('file_get_contents')) {
            $content = @file_get_contents($url);
        }

        if (!$content && function_exists('fopen') && function_exists('stream_get_contents')) {
            $handle  = @fopen ($url, "r");
            $content = @stream_get_contents($handle);
        }

        return $content;
    }


    /**
     * Hashes request parameters.
     *
     * @param $params
     * @return string
     */
    protected function hash($params)
    {
        $fields = array($params);

        return md5(serialize($fields));
    }
}