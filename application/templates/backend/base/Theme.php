<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\templates\backend\base;

use Yii;
use yii\helpers\Url;

class Theme extends \yii\base\Theme
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->getAssetManager()->bundles['mihaildev\ckeditor\Assets'] = [
            'sourcePath' => '@media/vendor/ckeditor',
            'js'         => [
                'ckeditor.js',
                'js.js',
            ],
        ];

        $rHostInfo = Url::home(true);
        if (strpos(Yii::$app->request->absoluteUrl, $rHostInfo.'admin/file-manager-elfinder') === false) {
            Yii::$app->getAssetManager()->bundles['yii\jui\JuiAsset'] = [
                'sourcePath' => '@app/media',
                'js'         => [
                    'js/jquery-ui.min.js',
                ],
                'css'        => [],
            ];
        }
        $theme = Yii::$app->keyStorage->get('themeBackend');

        $this->basePath = '@app/templates/backend/' . (!$theme ? 'base' : $theme);
        $this->baseUrl = '@web/templates/backend/' . (!$theme ? 'base' : $theme) . '/web';

        $this->pathMap = [
            '@app/views'   => '@app/templates/backend/' . (!$theme ? 'base' : $theme) . '/views',
            '@app/modules' => '@app/templates/backend/' . (!$theme ? 'base' : $theme) . '/modules',
            '@app/widgets' => '@app/templates/backend/' . (!$theme ? 'base' : $theme) . '/widgets',
        ];
    }
}
