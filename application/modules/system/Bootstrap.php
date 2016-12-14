<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: Bootstrap.php
 */

namespace app\modules\system;

use app\components\menu\MenuManager;
use app\components\module\ModuleQuery;
use app\modules\menu\models\MenuItem;
use app\modules\system\models\DbState;
use Yii;
use yii\base\BootstrapInterface;
use yii\caching\ExpressionDependency;
use yii\helpers\VarDumper;

class Bootstrap implements BootstrapInterface
{
    /**
     * @var null|\yii\caching\Dependency
     */
    private $_moduleConfigDependency;


    public function bootstrap($app)
    {
        /**
         *
         * @var Module                   $systemModule
         * @var \app\modules\user\Module $userModule
         *
         */

        $this->_moduleConfigDependency = new ExpressionDependency(['expression' => '\Yii::$app->getModulesHash()']);

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

        Yii::$container->set('app\components\MenuMap', [
            'cache'           => $app->cache,
            'cacheDependency' => DbState::dependency(MenuItem::tableName()),
        ]);

        Yii::$container->set('app\components\MenuUrlRule', [
            'cache'           => $app->cache,
            'cacheDependency' => $this->_moduleConfigDependency,
        ]);



        \Yii::setAlias('@frontendTemplate', realpath(__DIR__ . '/../../templates/frontend/' . $themeFrontend));

        // Індексація сайту
        if (!Yii::$app->keyStorage->get('indexing')) {
            \Yii::$app->getView()->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
        }
    }
}
