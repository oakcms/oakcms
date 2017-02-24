<?php

use YOOtheme\Widgetkit\Application;

class WP_Widget_Widgetkit extends WP_Widget
{
    public $app;

    public function __construct()
    {
        parent::__construct('', 'Widgetkit', array('description' => __('Display your widgets.', 'widgetkit'), 'settings' => array('title'  => '', 'widgetkit' => '{}')));

        $this->app = Application::getInstance();
    }

    public function widget($args, $instance)
    {
        $output   = array($args['before_widget']);
        $settings = array_merge($this->widget_options['settings'], $instance);

        if ($settings['title']) {
            array_push($output, $args['before_title'], $settings['title'], $args['after_title']);
        }

        array_push($output, $this->app->renderWidget(json_decode($settings['widgetkit'], true)), $args['after_widget']);

        echo implode('', $output);
    }

    public function form($instance)
    {
        $settings = array_merge($this->widget_options['settings'], $instance);
        $selected = json_decode($settings['widgetkit']);
        $widget   = isset($selected->widget) ? $this->app['widgets']->get($selected->widget) : null;

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'widgetkit'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($settings['title']); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>">
        </p>
        <p class="widgetkit-widget">
            <button class="button"><?php echo $widget ? 'Widget: '.$widget->getConfig('label') : 'Select Widget'; ?></button>
            <input type="hidden" name="<?php echo $this->get_field_name('widgetkit'); ?>" value="<?php echo esc_attr($settings['widgetkit']); ?>">
        </p>
        <?php
    }
}