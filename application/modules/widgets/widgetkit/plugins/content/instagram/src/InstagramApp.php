<?php

namespace YOOtheme\Widgetkit\Content\instagram;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;


class InstagramApp extends ApplicationAware
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

        $posts = array();

        // Cache invalid?
        if (array_key_exists('hash', $content) // never cached
            || $now - $content['hashed'] > $expires // cached values too old
            || md5(serialize($params)) != $content['hash']) // content settings have changed
        {

            $max_id = '';
            $url    = "http://instagram.com/%s/media?max_id=%s";

            do {

                $json = json_decode($this->url_get_contents(sprintf($url, $params['username'], $max_id)), true);

                foreach ($json['items'] as $item) {

                    $post = array(
                        'title' => $item['user']['full_name']." (".$item['user']['username'].")",
                        'content' => $item['caption']['text'],
                        'date' => date('d-m-Y H:i:s O', $item['caption']['created_time']),
                        'link' => $item['link'],
                        'location' => null,
                        'media' => $item['images']['standard_resolution']['url'],
                        'options' => array(
                            'media' => array(
                                'width' => $item['images']['standard_resolution']['width'],
                                'height' => $item['images']['standard_resolution']['height']
                            )
                        )
                    );

                    // seperate the hashtags
                    $post['content'] = preg_replace('/#/', ' #', $post['content']);
                    // make hashtags clickable
                    $post['content'] = preg_replace('/(?<=^|(?<=[^a-zA-Z0-9-_\.]))\#([\P{Z}]+)/', '<a href="https://instagram.com/explore/tags/$1">#$1</a>', $post['content']);

                    // make user names clickable
                    $post['content'] = preg_replace('/(?<=^|(?<=[^a-zA-Z0-9-_\.]))\@([\P{Z}]+)/', '<a href="https://instagram.com/$1">@$1</a>', $post['content']);

                    // convert emoticons to UTF-8 code
                    $post['content'] = mb_convert_encoding($post['content'], 'UTF-8');


                    if($item['type'] == 'video'){
                        $post['media'] = $item['videos']['standard_resolution']['url'];
                        $post['options']['media'] = array(
                            'poster' => $item['images']['standard_resolution']['url'],
                            'width'  => $item['videos']['standard_resolution']['width'],
                            'height' => $item['videos']['standard_resolution']['height']
                        );
                    }

                    if ($params['title'] == 'username'){
                        $post['title'] = $item['user']['username'];
                    } elseif($params['title'] == 'fullname'){
                        $post['title'] = $item['user']['full_name'];
                    }

                    $posts[] = $post;

                    if ($params['limit'] && (sizeof($posts) == $params['limit']) || sizeof($posts) == 60 ) break 2;
                }

                $endElement = end($json['items']);
                $max_id = $endElement['id'];

            } while (isset($json['more_available']) && $json['more_available'] == 1);

            // write cache
            $content['prepared'] = json_encode($posts);
            $content['hash'] = md5(serialize($params));
            $content['hashed'] = $now;
            unset($content['error']);

            $this->app['content']->save($content->toArray());

            return $posts;
        }

        // read from cache
        $posts = json_decode($content['prepared'], true);

        return $posts ? $posts: array();
    }

    protected function url_get_contents ($url) {

        $content = '';

        if (function_exists('curl_exec') && ini_get('open_basedir') === ''){
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

        if (!$content && function_exists('file_get_contents')){
            $content = @file_get_contents($url);
        }

        if (!$content && function_exists('fopen') && function_exists('stream_get_contents')){
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