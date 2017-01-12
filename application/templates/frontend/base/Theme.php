<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 12.12.2016
 * Project: oakcms
 * File name: Theme.php
 */

namespace app\templates\frontend\base;

use Yii;

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

        Yii::$app->getAssetManager()->bundles['yii\bootstrap\BootstrapAsset'] = [];
        Yii::$app->getAssetManager()->bundles['yii\bootstrap\BootstrapPluginAsset'] = [];

        $this->basePath = '@app/templates/frontend/base';
        $this->baseUrl = '@web/templates/frontend/base/web';

        $this->pathMap = [
            '@app/views'   => '@app/templates/frontend/base/views',
            '@app/modules' => '@app/templates/frontend/base/modules',
            '@app/widgets' => '@app/templates/frontend/base/widgets',
        ];
    }
}
