<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 12.12.2016
 * Project: oakcms
 * File name: Theme.php
 */

namespace app\templates\backend\base;

use Yii;

class Theme extends \yii\base\Theme {

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->getAssetManager()->bundles['yii\bootstrap\BootstrapAsset'] = [];
        Yii::$app->getAssetManager()->bundles['yii\bootstrap\BootstrapPluginAsset'] = [];

        $theme = Yii::$app->keyStorage->get('themeBackend');

        $this->basePath = '@app/templates/backend/' . (!$theme ? 'base' : $theme);
        $this->baseUrl = '@web/templates/backend/' . (!$theme ? 'base' : $theme). '/web';

        $this->pathMap = [
            '@app/views'   => '@app/templates/backend/' . (!$theme ? 'base' : $theme) . '/views',
            '@app/modules' => '@app/templates/backend/' . (!$theme ? 'base' : $theme) . '/modules',
            '@app/widgets' => '@app/templates/backend/' . (!$theme ? 'base' : $theme) . '/widgets',
        ];
    }
}
