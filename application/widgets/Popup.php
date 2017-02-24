<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\widgets;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;

/**
 * Class Popup
 *
 *  Usage
 *
 *  1. Ajax
 *  echo Popup::widget([
 *      'label' => 'open popup',
 *      'options' => ['class' => 'btn btn-default'],
 *      'popupOptions' => ['width' => '400', 'backdrop' => 'static'],
 *      'url' => ['some/ajax/page']
 *  ]);
 *
 *  2. String
 *  echo Popup::widget([
 *      'label' => 'open popup',
 *      'options' => ['class' => 'btn btn-default'],
 *      'popupOptions' => ['width' => '400', 'backdrop' => 'static'],
 *      'content' => '<p>Hello World!</p>'  // контент должен распознаваться jQuery() как DOM элемент
 *  ]);
 *
 *  3. Html
 *  Popup::begin([
 *      'label' => 'open popup',
 *      'options' => ['tag' => 'span', 'class' => 'btn btn-default'],
 *      'popupOptions' => ['width' => '400', 'backdrop' => 'static']
 *  ]);
 *
 *  echo 'Popup content here';
 *
 *  Popup::end();
 *
 * @package yii2-widgets
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Popup extends \yii\base\Widget
{
    const CONTEXT_LINK = 'link';
    const CONTEXT_CONTENT = 'content';
    const CONTEXT_SOURCE = 'source';

    /**
     * Button options
     * @var array
     */
    public $options = [
        'class' => 'btn btn-default'
    ];
    /**
     * Button content
     * @var string
     */
    public $label = 'Show';
    /**
     * @var array
     *  - backdrop
     *  - keyboard
     *  - width
     *  - height
     *  - class
     *  - style
     */
    public $popupOptions = [];
    /**
     * @var array|string
     */
    public $url;
    /**
     * @var string
     */
    public $content;

    private $_context;

    public function init()
    {
        $this->options['id'] = $this->getId();
        $this->options['data-behavior'] = 'grom-popup';

        if (is_array($this->popupOptions) && !empty($this->popupOptions)) {
            $this->options['data-popup'] = $this->popupOptions;
        }

        $tag = ArrayHelper::remove($this->options, 'tag', 'a');

        if (!empty($this->url)) {
            echo Html::a($this->label, $this->url, $this->options);

            $this->_context = self::CONTEXT_LINK;
        } elseif (!empty($this->content)) {
            $this->options['data-popup-content'] = $this->content;

            echo Html::tag($tag, $this->label, $this->options);

            $this->_context = self::CONTEXT_CONTENT;
        } else {
            $sourceId = 'popup-source-' . $this->id;
            $this->options['data-popup-source'] = "#{$sourceId}";

            echo Html::tag($tag, $this->label, $this->options);

            $this->_context = self::CONTEXT_SOURCE;

            echo Html::beginTag('div', ['id' => $sourceId, 'class' => 'popup-source-wrapper', 'style' => 'display: none']);
        }
    }

    public function run()
    {
        if ($this->_context === self::CONTEXT_SOURCE) {
            echo Html::endTag('div');
        }

        $this->getView()->registerAssetBundle(PopupAsset::className());
    }
}
