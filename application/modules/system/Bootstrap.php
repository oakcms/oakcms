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

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        /**
         *
         * @var Module $systemModule
         * @var \app\modules\user\Module $userModule
         *
         */
        if($app->hasModule('system') && ($systemModule = $app->getModule('system')) instanceof Module) {
            // Установка теми з настроек сайту
            $themeFrontend = Yii::$app->keyStorage->get('themeFrontend');
            \Yii::$app->set('view', [
                'class' => 'app\components\FrontendView',
                'title' => 'Frontend Template',
                'enableMinify' => false,
                'web_path' => '@web',
                'base_path' => '@webroot',
                'minify_path' => '@webroot/assets',
                'js_position' => [ \yii\web\View::POS_HEAD ],
                'force_charset' => 'UTF-8',
                'expand_imports' => !YII_ENV_DEV,
                'compress_output' => !YII_ENV_DEV,
                'compress_options' => ['extra' => !YII_ENV_DEV],
                'concatCss' => !YII_ENV_DEV,
                'minifyCss' => !YII_ENV_DEV,
                'concatJs' => !YII_ENV_DEV,
                'minifyJs' => !YII_ENV_DEV,

                'theme' => [
                    'basePath' => '@app/templates/frontend/' . $themeFrontend,
                    'baseUrl' => '@web/templates/frontend/' . $themeFrontend . '/web',
                    'pathMap' => [
                        '@app/views' => '@app/templates/frontend/' . $themeFrontend . '/views',
                        '@app/modules' => '@app/templates/frontend/' . $themeFrontend . '/modules',
                        '@app/widgets' => '@app/templates/frontend/' . $themeFrontend . '/widgets'
                    ],
                ],
                'as seo' => [
                    'class' => 'app\modules\system\components\SeoViewBehavior',
                ]
            ]);

            $assetManager = [
                'class' => 'yii\web\AssetManager',
                'linkAssets' => false,
                //'forceCopy' => true,
                'appendTimestamp' => YII_ENV_DEV,
                'converter' => [
                    'class' => 'nizsheanez\assetConverter\Converter',
                    'destinationDir' => 'css/../',
                    'parsers' => [
                        'sass' => [
                            'class' => 'nizsheanez\assetConverter\Sass',
                            'output' => 'css',
                            'options' => [
                                'cachePath' => '@app/runtime/cache/sass-parser'
                            ],
                        ],
                        'scss' => [
                            'class' => 'nizsheanez\assetConverter\Scss',
                            'output' => 'css',
                            'options' => [],
                        ],
                        'less' => [
                            'class' => 'nizsheanez\assetConverter\Less',
                            'output' => 'css',
                            'options' => [
                                'auto' => true,
                            ]
                        ]
                    ]
                ],
            ];
            $assetManager['bundles'] = [
                'yii\jui\JuiAsset' => [
                    'sourcePath' => '@app/media/',
                    'js' => [
                        'js/jquery-ui.min.js'
                    ],
                    'css' => []
                ],
            ];
            if(strpos(Yii::$app->request->pathInfo, 'admin') !== 0) {
                $assetManager['bundles'] = [
                    'yii\bootstrap\BootstrapAsset' => [
                        //'css' => [],
                    ],
                ];
            }

            \Yii::$app->set('assetManager', $assetManager);
            \Yii::setAlias('@frontendTemplate', realpath(__DIR__ . '/../../templates/frontend/'.$themeFrontend));

            // Індексація сайту
            if(!Yii::$app->keyStorage->get('indexing')) {
                \Yii::$app->getView()->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
            }
        }
    }
}
