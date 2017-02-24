<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace YOOtheme\Widgetkit\Framework\Yii;

class Update
{
    /**
     * @var array
     */
    public $plugins = array();

    /**
     * @var array
     */
    public $themes = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $self = $this;

        add_filter('site_transient_update_plugins', function($transient) use ($self) {

            foreach ($self->plugins as $name => $update) {
                $self->prepare($transient, $update);
            }

            return $transient;
        });

        add_filter('site_transient_update_themes', function($transient) use ($self) {

            foreach ($self->themes as $name => $update) {
                $self->prepare($transient, $update);
            }

            return $transient;
        });

        add_filter('pre_set_site_transient_update_plugins', function($transient) use ($self) {

            foreach ($self->plugins as $name => $update) {
                $self->check($transient, $update);
            }

            return $transient;
        });

        add_filter('pre_set_site_transient_update_themes', function($transient) use ($self) {

            foreach ($self->themes as $name => $update) {
                $self->check($transient, $update);
            }

            return $transient;
        });

        add_filter('plugins_api', function($result, $action, $args) use ($self) {
            return $action == 'plugin_information' && isset($self->plugins[$args->slug]) ? $self->fetchData($self->plugins[$args->slug]) : false;
        }, 10, 3);
    }

    /**
     * Register plugin/theme update.
     *
     * @param string $name
     * @param string $type
     * @param string $remote
     * @param array  $options
     */
    public function register($name, $type, $remote, array $options = array())
    {
        $options = array_merge(compact('name', 'type', 'remote'), $options);

        if (!isset($options['id'])) {
            $options['id'] = $type == 'plugin' ? "$name/$name.php" : $name;
        }

        if ($type == 'plugin') {
            $this->plugins[$name] = $options;
        } else {
            $this->themes[$name] = $options;
        }

        // check expiration
        if (isset($options['expiration'])) {

            $timeout = $options['expiration'];

            if ($type == 'plugin' and $transient = get_site_transient('update_plugins') and isset($transient->response[$options['id']], $transient->last_checked)) {
                if ((time() - $transient->last_checked) > $timeout) {
                    delete_site_transient('update_plugins');
                }
            }

            if ($type == 'theme' and $transient = get_site_transient('update_themes') and isset($transient->response[$options['id']], $transient->last_checked)) {
                if ((time() - $transient->last_checked) > $timeout) {
                    delete_site_transient('update_themes');
                }
            }
        }
    }

    /**
     * Prepare update data.
     *
     * @param mixed $transient
     * @param array $update
     */
    public function prepare($transient, array $update)
    {
        if (isset($transient->response[$update['id']]) && $data = $transient->response[$update['id']]) {

            if (isset($update['key'])) {
                $data->package = add_query_arg(array('key' => $update['key']), $data->package);
            }

            $data->new_version = $data->version;

            $transient->response[$update['id']] = $update['type'] == 'plugin' ? $data : (array) $data;
        }
    }

    /**
     * Check if update is available.
     *
     * @param mixed $transient
     * @param array $update
     */
    public function check($transient, array $update)
    {
        if (isset($transient->checked[$update['id']]) and $data = $this->fetchData($update) and version_compare($transient->checked[$update['id']], $data->version, '<')) {
            $transient->response[$update['id']] = $data;
        }
    }

    /**
     * Fetches the update data from remote server.
     *
     * @param  array  $update
     * @return object
     */
    public function fetchData(array $update)
    {
        $remote = add_query_arg(array('user-agent' => true), $update['remote']);

        if ($response = wp_remote_retrieve_body(wp_remote_get($remote)) and $data = json_decode($response)) {

            $data->slug     = $update['name'];
            $data->url      = isset($update['url']) ? $update['url'] : '';
            $data->sections = isset($data->sections) ? (array) $data->sections : array();
            $data->banners  = isset($data->banners) ? (array) $data->banners : array();

            return $data;
        }

        return false;
    }
}
