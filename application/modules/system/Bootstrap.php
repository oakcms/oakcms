<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: Bootstrap.php
 */

namespace app\modules\system;

use Yii;
use yii\base\BootstrapInterface;
use yii\caching\ExpressionDependency;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        /**
         *
         * @var Module                   $systemModule
         * @var \app\modules\user\Module $userModule
         *
         */

        // Установка теми з настроек сайту
        $themeFrontend = Yii::$app->keyStorage->get('themeFrontend');

        \Yii::$app->getView()->title = Yii::$app->keyStorage->get('siteName');

        $assetManager = [
            'class'           => 'yii\web\AssetManager',
            'linkAssets'      => false,
            //'forceCopy' => true,
            'appendTimestamp' => YII_ENV_DEV,
            'converter'       => [
                'class'          => 'nizsheanez\assetConverter\Converter',
                'destinationDir' => 'css/../',
                'parsers'        => [
                    'sass' => [
                        'class'   => 'nizsheanez\assetConverter\Sass',
                        'output'  => 'css',
                        'options' => [
                            'cachePath' => '@app/runtime/cache/sass-parser',
                        ],
                    ],
                    'scss' => [
                        'class'   => 'nizsheanez\assetConverter\Scss',
                        'output'  => 'css',
                        'options' => [],
                    ],
                    'less' => [
                        'class'   => 'nizsheanez\assetConverter\Less',
                        'output'  => 'css',
                        'options' => [
                            'auto' => true,
                        ],
                    ],
                ],
            ],
        ];

        $assetManager['bundles'] = [
            'yii\jui\JuiAsset' => [
                'sourcePath' => '@app/media/',
                'js'         => [
                    'js/jquery-ui.min.js',
                ],
                'css'        => [],
            ],
        ];

        $app->set('assetManager', $assetManager);
        $themeClass = '\app\templates\frontend\\' . $themeFrontend . '\Theme';

        \Yii::$app->getView()->theme = new $themeClass;

        \Yii::setAlias('@frontendTemplate', realpath(__DIR__ . '/../../templates/frontend/' . $themeFrontend));

        // Індексація сайту
        if (!Yii::$app->keyStorage->get('indexing')) {
            \Yii::$app->getView()->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
        }
    }
}
