<?php

namespace YOOtheme\Widgetkit\Content\Twitter;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

use Codebird\Codebird;


class TwitterOAuth extends ApplicationAware
{

    /**
     * Constructor.
     *
     * @param Application $app
     * @param $credentials
     */
    public function __construct(Application $app, $credentials)
    {
        $this->app = $app;
        $this->credentials = $credentials;
    }

    /**
     * Generates twitter authorisation URI.
     *
     * @return array
     * @throws \Exception
     */
    public function getAuthorisationUri()
    {
        $cb = $this->connect();

        $response = $cb->oauth_requestToken(array(
            'oauth_callback' => 'oob'
        ));

        if ($response->httpstatus != 200) {
            throw new \Exception($response->errors[0]->message);
        }

        $cb->setToken($response->oauth_token, $response->oauth_token_secret);

        return array(
            'token' => array(
                'token' => $response->oauth_token,
                'secret' => $response->oauth_token_secret
            ),
            'redirect_uri' => $cb->oauth_authorize()
        );
    }

    /**
     * Resolve PIN to token.
     *
     * @param $pin
     * @param $token
     * @return mixed
     * @throws \Exception
     */
    public function resolveAuthPin($pin, $token)
    {
        if (!isset($token['token']) || !$token['token'] || !isset($token['secret']) || !$token['secret']) {
            throw new \Exception("Invalid PIN or session expired.");
        }

        $cb = $this->connect();
        $cb->setToken($token['token'], $token['secret']);

        $response = $cb->oauth_accessToken(array(
            'oauth_verifier' => $pin
        ));

        if ($response->httpstatus != 200) {
            throw new \Exception($response->message);
        }

        return $response;
    }

    /**
     * Creates Codebird instance
     *
     * @return Codebird
     */
    public function connect()
    {
        Codebird::setConsumerKey($this->credentials['consumer_key'], $this->credentials['consumer_secret']);

        return Codebird::getInstance();
    }

    /**
     * Issues the API.
     *
     * @param $method
     * @param array $params
     * @param array $token
     * @return array|mixed
     * @throws \Exception
     */
    public function get($method, $params = array(), $token = array())
    {
        if (!isset($token['token']) || !$token['token'] || !isset($token['secret']) || !$token['secret']) {
            throw new \Exception('Please check your Twitter Settings.');
        }

        $cb = $this->connect();
        $cb->setToken($token['token'], $token['secret']);

        if ($method === 'statuses/user_timeline') {

            $params = array_merge($params, array('count' => 200));

            $response = $cb->statuses_userTimeline($params);
            if ($response->httpstatus != 200) {
                throw new \Exception($response->errors[0]->message);
            }
            $tweets = (array)$response;

        } elseif ($method === 'search/tweets') {

            $params = array_merge($params, array('count' => 200));

            $response = $cb->search_tweets($params);
            if ($response->httpstatus != 200) {
                throw new \Exception($response->errors[0]->message);
            }
            $tweets = (array)($response->statuses);

        } else {
            throw new \Exception('Unknown API method');
        }

        // only keep real tweets
        $tweets = array_filter($tweets, function ($status) {
            return isset($status->id);
        });

        // object -> array recursive
        $tweets = json_decode(json_encode($tweets), true);

        return $tweets;

    }

    /**
     * Fetches tweets from cache or API.
     *
     * @param $method
     * @param $token
     * @param $params
     * @param $content
     * @return array|mixed
     * @throws \Exception
     */
    public function fetch($method, $token, $params, $content)
    {
        // Cache settings
        $now = time();
        $expires = 5 * 60;

        $tweets = array();

        // Cache invalid?
        if (array_key_exists('hash', $content) // never cached
            || $now - $content['hashed'] > $expires // cached values too old
            || $this->hash($method, $params, $token) != $content['hash'] // content settings have changed
        ) {

            try {
                $response = $this->get($method, $params, $token);

                // create black list pattern for preg_match
                $blacklist = trim($content['blacklist']) ? explode(",", trim(str_replace(', ', ',', $content['blacklist']))) : array();

                if (count($blacklist)) {

                    $blacklist = array_map(function ($word) {
                        return preg_quote($word);
                    }, $blacklist);

                    $pattern = "/(" . implode('|', $blacklist) . ")/i";
                }

                $count = 0;

                foreach ($response as $item) {

                    $tweet = $this->prepare($item, $content);

                    // check maximum tweet count
                    if ($count >= $content['limit']) {
                        break;
                    }

                    // filter out text-only tweets if desired
                    if ($content['only_media'] && !$tweet['media']) {
                        continue;
                    }

                    // check for words if blacklist is not empty
                    if (count($blacklist) && preg_match($pattern, $tweet['content'])) {
                        continue;
                    }

                    $tweets[] = $tweet;
                    $count++;
                }

                // write cache
                $content['prepared'] = json_encode($tweets);
                $content['hash'] = $this->hash($method, $params, $token);
                $content['hashed'] = $now;
                unset($content['error']);

                $this->app['content']->save($content->toArray());

                return $tweets;
            } catch (\Exception $e) {
                // Fallback to cache and log of API error
                $content['error'] = $e->getMessage();
                $this->app['content']->save($content->toArray());
            }
        }

        // read from cache
        $tweets = json_decode($content['prepared'], true);

        return $tweets ? $tweets : array();
    }

    /**
     * Prepares tweets for displaying.
     *
     * @param $tweet
     * @param $content
     * @return array
     */
    public function prepare($tweet, $content)
    {

        $utc = new \DateTimeZone('UTC');
        $created = \DateTime::createFromFormat('D M d H:i:s T Y', $tweet['created_at'], $utc);

        $item = array(
            'title' => $this->escape($tweet['user']['name']),
            'content' => $this->escape($tweet['text']),
            'date' => $created->format('d-m-Y H:i:s O'),
            'link' => sprintf('https://twitter.com/%s/status/%s', $this->escape($tweet['user']['screen_name']), $tweet['id_str']),
            'location' => null,
            'media' => null
        );

        if ($content['title'] == 'screen_name') {
            $item['title'] = '@' . $this->escape($tweet['user']['screen_name']);
        }
        if ($content['title'] == 'combined') {
            $item['title'] = sprintf("%s (@%s)", $this->escape($tweet['user']['name']), $this->escape($tweet['user']['screen_name']));
        }

        // make links clickable
        if (isset($tweet['entities']['urls']) && $urls = $tweet['entities']['urls']) {
            foreach ($urls as $url) {
                $item['content'] = str_replace($url['url'], sprintf('<a href="%s">%s</a>', $this->escape($url['expanded_url']), $this->escape($url['display_url'])), $item['content']);
            }
        }

        // make hashtags clickable
        if (isset($tweet['entities']['hashtags']) && $hashtags = $tweet['entities']['hashtags']) {
            foreach ($hashtags as $hashtag) {
                $item['content'] = str_replace('#' . $hashtag['text'], sprintf('<a href="https://twitter.com/hashtag/%s">#%s</a>', $hashtag['text'], $hashtag['text']), $item['content']);
            }
        }

        // make user names clickable
        $item['content'] = preg_replace('/(?<=^|(?<=[^a-zA-Z0-9-_\.]))\@([a-zA-Z\d_]+)/', '<a href="https://twitter.com/$1">@$1</a>', $item['content']);

        // some tweets have an exact position attached
        if ($tweet['coordinates'] && $point = $tweet['coordinates']['coordinates']) {
            $item['location'] = array('lng' => $point[0], 'lat' => $point[1]);
        } // others have a less specific polygon attached
        elseif ($tweet['place'] && $polygon = $tweet['place']['bounding_box']['coordinates']) {
            $item['location'] = $this->centroid($polygon[0]);
        }

        if (isset($tweet['entities']) && $tweet['entities'] && isset($tweet['entities']['media']) && $tweet['entities']['media'] && ($media = $tweet['entities']['media']) && count($media) > 0) {
            $item['media'] = $media[0]['media_url'];

            // remove media link from tweet content
            $item['content'] = str_replace($media[0]['url'], '', $item['content']);
        }

        if (!isset($item['media'])) {
            // maybe we have a youtube url to use instead?
            if (isset($tweet['entities']['urls']) && $urls = $tweet['entities']['urls']) {
                foreach ($urls as $url) {
                    if (preg_match('/youtube\.com\/watch/', $url['expanded_url'])) {
                        $item['media'] = $url['expanded_url'];
                    }
                }
            }
        }

        return $item;
    }

    /**
     * @param $str
     * @return string
     */
    protected function escape($str)
    {
        return htmlspecialchars($str, ENT_COMPAT, 'UTF-8', false);
    }

    /**
     * Calculates the geometric center of a polygon.
     *
     * @param $polygon
     * @return array
     */
    protected function centroid($polygon)
    {
        $n = count($polygon);
        $geo = array_reduce($polygon, function ($carry, $item) {
            return array($carry[0] + $item[0], $carry[1] + $item[1]);
        });
        $geo = array($geo[0] / $n, $geo[1] / $n);

        return array('lng' => $geo[0], 'lat' => $geo[1]);
    }

    /**
     * Hashes request parameters.
     *
     * @param $method
     * @param $params
     * @param $token
     * @return string
     */
    protected function hash($method, $params, $token)
    {
        $fields = array($method, $params, $token);

        return md5(serialize($fields));
    }

}
