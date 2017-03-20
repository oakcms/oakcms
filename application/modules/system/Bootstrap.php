<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: Bootstrap.php
 */

namespace app\modules\system;

use app\modules\menu\events\MenuItemLayoutsModuleEvent;
use app\modules\menu\Module;
use yii\base\Event;
use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\Url;

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

        \Yii::$app->getView()->title = Yii::$app->keyStorage->get('siteName');

        $assetManager = [
            'class'           => 'yii\web\AssetManager',
            'linkAssets'      => false,
            //'forceCopy' => true,
            'appendTimestamp' => YII_ENV_DEV,
            'converter'       => [
                'class'          => 'nizsheanez\assetConverter\Converter',
                'destinationDir' => 'compiled',
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

        $app->set('assetManager', $assetManager);

        if (!Yii::$app->request->isConsoleRequest) {
            $rHostInfo = Url::home(true);
            $themeBackend = Yii::$app->keyStorage->get('themeBackend');
            $themeFrontend = Yii::$app->keyStorage->get('themeFrontend');
            \Yii::setAlias('@backendTemplate', realpath(__DIR__ . '/../../templates/backend/' . $themeBackend));
            \Yii::setAlias('@frontendTemplate', realpath(__DIR__ . '/../../templates/frontend/' . $themeFrontend));

            if (strpos(Yii::$app->request->absoluteUrl, $rHostInfo . 'admin') !== false) {
                $themeClass = '\app\templates\backend\\' . $themeBackend . '\Theme';
                \Yii::$app->getView()->theme = new $themeClass;
                Yii::$app->getErrorHandler()->errorAction = '/admin/default/error';
            } else {
                $themeClass = '\app\templates\frontend\\' . $themeFrontend . '\Theme';
                \Yii::$app->getView()->theme = new $themeClass;
            }

            // Індексація сайту
            if (!Yii::$app->keyStorage->get('indexing')) {
                \Yii::$app->getView()->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
            }
        }
    }
}
