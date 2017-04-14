<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use Yii;
use yii\helpers\Url;

/**
 * <a href="/some/url" data-behavior="iframe" data-iframe-method="get" data-iframe-handler="function(data){}" data-popup="{backdrop: 'static', keyboard: false}" data-params="{a:b}">push</a>
 */
class ModalIFrame extends \yii\base\Widget
{
    /**
     * Настройки кнопки/ссылки
     * @var array
     *  - html аттрибуты
     *  - queryHandler
     */
    public $options;
    /**
     * Настройки контейнера попапа
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
     * Настройки айфрейма
     * @var array
     *  - width
     *  - height auto
     *  - dataHandler
     *  - actionHandler
     *  - paramsHandler
     */
    public $iframeOptions = [];
    /**
     * Настройки формы
     * @var array
     *  - method    get/post
     *  - params    массив с параметрами формы
     *  - paramsHandler
     */
    public $formOptions;
    /**
     * @var array|string
     */
    public $url;
    /**
     * @var string
     */
    public $label = 'Show';
    /**
     * Js функция для обработки данным передаваемых из айфрейма родительскому окну
     * @var string
     *
     * пример: function(data) { $("#input").val(data.value) }
     */
    public $dataHandler;
    /**
     * Js функция для модификации урла страницы которая будет отображена в айфрейме
     * @var string
     *
     * пример: function(url) { return url + "&foo=bar" }
     */
    public $actionHandler;
    /**
     * Js функция для модификаци параметров формы перед ее отправкой
     * @var string
     *
     * пример: function(params) { params.foo = "bar" }
     */
    public $paramsHandler;

    public function run()
    {
        $this->initOptions();

        $tag = ArrayHelper::remove($this->options, 'tag', 'a');

        if ($tag == 'a') {
            echo Html::a($this->label, $this->url, $this->options);
        } else {
            $this->options['data']['href'] = Url::to($this->url);
            echo Html::tag($tag, $this->label, $this->options);
        }

        $this->getView()->registerAssetBundle(ModalIFrameAsset::className());
    }


    /**
     * @inheritdoc
     */
    protected function initOptions()
    {
        if (is_array($this->popupOptions)) {
            $this->options['data']['popup'] = $this->popupOptions;
        }

        if (is_array($this->iframeOptions)) {
            $this->options['data']['iframe'] = $this->iframeOptions;
        }

        if (is_array($this->formOptions)) {
            $this->options['data']['form'] = $this->formOptions;
        }

        $this->options['data']['behavior'] = 'iframe';
        if (isset($this->dataHandler)) {
            $this->options['data']['data-handler'] = $this->dataHandler;
        }

        if (isset($this->actionHandler)) {
            $this->options['data']['action-handler'] = $this->actionHandler;
        }

        if (isset($this->paramsHandler)) {
            $this->options['data']['params-handler'] = $this->paramsHandler;
        }
    }

    /**
     * JS код для отправки данных в родительское окно
     * @param $data
     * @param bool $closePopup
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function postDataJs($data, $closePopup = true)
    {
        Yii::$app->view->registerAssetBundle(ModalIFrameAsset::className());

        return "yii.gromverIframe.postData(" . Json::encode($data) . ");" . ($closePopup ? "yii.gromverIframe.closePopup();" : "");
    }

    /**
     * Отправить данные в родительское онко и завершить приложение
     * @param $data
     * @param bool $closePopup
     * @throws \yii\base\ExitException
     */
    public static function postData($data, $closePopup = true)
    {
        echo self::postMessageFunction();
        echo Html::script("postIframeMessage('send.grom.iframe', " . Json::encode($data) . ");");
        if ($closePopup) {
            echo Html::script("postIframeMessage('close.grom.iframe');");
        }

        Yii::$app->end();
    }

    /**
     * Обновить страницу и закрыть приложение
     * @throws \yii\base\ExitException
     */
    public static function refreshParent()
    {
        echo self::postMessageFunction();
        echo Html::script("postIframeMessage('refreshParent.grom.iframe');");

        Yii::$app->end();
    }

    /**
     * Сменить url страницы и закрыть приложение
     * @throws \yii\base\ExitException
     */
    public static function redirectParent($url)
    {
        echo self::postMessageFunction();
        echo Html::script("postIframeMessage('redirectParent.grom.iframe', " . json_encode(Url::to($url)) . ");");

        Yii::$app->end();
    }

    /**
     * Поставить задачу на обновление страницы после закрытия модального окна
     * @throws \yii\base\InvalidConfigException
     */
    public static function refreshParentOnClose()
    {
        Yii::$app->view->registerAssetBundle(ModalIFrameAsset::className());

        Yii::$app->view->registerJs(<<<JS
$(yii.gromverIframe).on('closePopup.grom.iframe', function() {
    yii.gromverIframe.refreshParent();
});
JS
        );
    }

    /**
     * JS функция хелпер для постинга сообщений, используеться в [[self::refreshParent]]
     * @return string
     */
    private static function postMessageFunction()
    {
        return Html::script(
            <<<JS
                function postIframeMessage(name, message, target) {
        var data = {
            name: name,
            message: message
        };

        (target || window.parent).postMessage(JSON.stringify(data), window.location.origin || window.location.href);
    }
JS
        );
    }
}
