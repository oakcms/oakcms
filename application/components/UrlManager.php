<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\components;

use app\modules\language\models\Language;

class UrlManager extends \codemix\localeurls\UrlManager {
    const LANGUAGE_PARAM = '__language';
    public $languages;

    public function init()
    {
        $this->languages = Language::getAllLang();
        $this->languageParam = self::LANGUAGE_PARAM;
        parent::init();
    }

    protected function processLocaleUrl($request) {
        parent::processLocaleUrl($request);
        \Yii::$app->cache->flush();
    }
}
