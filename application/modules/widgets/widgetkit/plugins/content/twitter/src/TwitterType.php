<?php

namespace YOOtheme\Widgetkit\Content\Twitter;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\Routing\ControllerInterface;
use YOOtheme\Widgetkit\Content\Type;


class TwitterType extends Type implements ControllerInterface
{

    /**
     * @param Application $app
     */
    public function main(Application $app)
    {
        parent::main($app);

        $app->on('init', function ($event, $app) {

            $app['twitter'] = function () use ($app) {
                $credentials = $app['plugins']->get('content/twitter')->config['credentials'];

                return new TwitterOAuth($app, $credentials);
            };

        }, -5);

        $this['controllers']->add($this);
    }

    /**
     * Redirects to twitter authorisation endpoint.
     *
     * @return response
     */
    public function redirectAction()
    {
        $data = $this['twitter']->getAuthorisationUri();

        $this['session']->set('twitter_token', $data['token']);

        return $this['response']->redirect($data['redirect_uri']);
    }

    /**
     * Resolve PIN to token action.
     *
     * @param $pin
     * @return response
     */
    public function pinResolveAction($pin)
    {
        $token = $this['session']->get('twitter_token', array());

        try {
            $response = $this['twitter']->resolveAuthPin($pin, $token);
            $token = array(
                'token' => $response->oauth_token,
                'secret' => $response->oauth_token_secret
            );

            $this['option']->set('twitter_token', $token);

            return $this['response']->json(array('success' => true));
        } catch (\Exception $e) {
            return $this['response']->json(array('success' => false, 'message' => $e->getMessage()), 400);
        }
    }

    /**
     * Deletes a token from the database.
     *
     * @return response
     */
    public function tokenDeleteAction()
    {
        unset($this['option']['twitter_token']);

        return $this['response']->json(array('success' => true));
    }

    public static function getRoutes()
    {
        return array(
            array('twitter_auth', 'redirectAction', 'GET', array('access' => 'manage_widgetkit')),
            array('twitter_auth', 'pinResolveAction', 'POST', array('access' => 'manage_widgetkit')),
            array('twitter_auth', 'tokenDeleteAction', 'DELETE', array('access' => 'manage_widgetkit'))
        );
    }
}