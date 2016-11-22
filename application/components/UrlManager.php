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
use yii\helpers\VarDumper;

class UrlManager extends \yii\web\UrlManager

{
    public $languages;

    public function init()
    {
        $this->languages = Language::getAllLang();

        parent::init();
    }

    public function createUrl($params)
    {
        if(isset($params['q'])) {
            unset($params['q']);
        }
        return parent::createUrl($params);
    }

    /*protected function processLocaleUrl($request) {
        \Yii::$app->cache->flush();
        parent::processLocaleUrl($request);
    }*/
}
