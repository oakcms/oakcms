<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\widgets;


use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Айфрейм с отложенной загрузкой
 * Class LazyLoadIFrame
 * @package yii2-widgets
 */
class LazyLoadIFrame extends Widget {
    public $src;
    public $options = [
        'frameborder' => 0,
        'allowfullscreen' => true
    ];
    public $spinner = false;

    public function init()
    {
        if (!isset($this->src)) {
            throw new InvalidConfigException(__CLASS__ . '::src must be set.');
        }

        Html::addCssClass($this->options, 'lazy');
        $this->options['data-src'] = $this->src;

        $this->view->registerAssetBundle(LazyLoadAsset::className());
    }

    public function run()
    {
        echo Html::tag('iframe', '', $this->options);
        $this->options['src'] = ArrayHelper::remove($this->options, 'data-src');
        Html::removeCssClass($this->options, 'lazy');
        echo '<noscript>' . Html::tag('iframe', '', $this->options) . '</noscript>';
    }
}
