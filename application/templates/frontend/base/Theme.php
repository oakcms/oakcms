<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 12.12.2016
 * Project: oakcms
 * File name: Theme.php
 */

namespace app\templates\frontend\base;

use Yii;
use yii\web\View;

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
