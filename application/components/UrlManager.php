<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 24.09.2016
 * Project: osnovasite
 * File name: UrlManager.php
 */
namespace app\components;

use app\modules\language\models\Language;
use yii\helpers\ArrayHelper;

class UrlManager extends \codemix\localeurls\UrlManager {
    const LANGUAGE_PARAM = 'language';
    public $languages;
    private $_language;

    public function init()
    {
        $this->languages = Language::getAllLang();

        parent::init();
    }

    /**
     * @param array|string $params
     * @param null|string $language языковой контекст обработки урла, позволяет определить для какого сайта(рускоязычного или допустим англоязычного)
     * нужно сделать урл, используется в MenuManager для определения соответсвующей карты меню
     * @return string
     */
    public function createUrl($params, $language = null)
    {
        if(isset($params['q'])) {
            unset($params['q']);
        }
//
//        $this->_language = isset($language) ? $language : ArrayHelper::getValue($params, static::LANGUAGE_PARAM, \Yii::$app->language);
//
//        if(is_array($params)) {
//            unset($params[static::LANGUAGE_PARAM]);
//        }

        return parent::createUrl($params);
    }

    protected function processLocaleUrl($request) {
        parent::processLocaleUrl($request);
        \Yii::$app->cache->flush();
    }
}
