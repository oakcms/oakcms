<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */


namespace app\templates\frontend\base;

/**
 * Class Theme
 */
class Theme extends \app\components\ThemeFrontend
{

    public static $menuItemLayouts = [
        '@frontendTemplate/views/layouts/_clear'  => 'clear',
        '@frontendTemplate/views/layouts/content' => 'content',
        '@frontendTemplate/views/layouts/main'    => 'main'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

//        Yii::$app->getAssetManager()->bundles['yii\bootstrap\BootstrapAsset'] = [
//            'css' => []
//        ];
//        Yii::$app->getAssetManager()->bundles['yii\bootstrap\BootstrapPluginAsset'] = [
//            'js' => []
//        ];
//        Yii::$app->getAssetManager()->bundles['yii\web\JqueryAsset'] = [
//            'js' => ['//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js'],
//            'jsOptions' => ['position' => View::POS_HEAD]
//        ];

        $this->basePath = '@app/templates/frontend/base';
        $this->baseUrl = '@web/templates/frontend/base/web';

        $this->pathMap = [
            '@app/views'   => '@app/templates/frontend/base/views',
            '@app/modules' => '@app/templates/frontend/base/modules',
            '@app/widgets' => '@app/templates/frontend/base/widgets',
        ];
    }
}
