<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$basePath = dirname(__DIR__);
$webRoot = dirname($basePath);

$config = [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'runtimePath'         => $webRoot . '/runtime',
    'vendorPath'          => $webRoot . '/vendor',
    'bootstrap'           => [
        'log',
        'app\modules\system\Bootstrap',
        'app\modules\admin\Bootstrap',
    ],
    'modules'             => [
        'admin' => [
            'class'               => 'app\modules\admin\Module',
            'controllerNamespace' => 'app\modules\admin\controllers\console',
        ],
    ],
    'controllerMap'       => [
        'migrate' => [
            'class' => '\app\commands\MigrateController',
        ],
    ],
    'components'          => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'keyStorage'   => [
            'class' => 'app\components\KeyStorage',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'log' => [
            'targets' => [
                'db'=>[
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except'=>['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                    'prefix'=>function () {
                        $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                        return sprintf('[%s][%s]', Yii::$app->id, $url);
                    },
                    'logVars'=>[],
                    'logTable'=>'{{%admin_system_log}}'
                ]
            ],
        ],
        'i18n'         => [
            'translations' => [
                '*' => [
                    'class'   => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
