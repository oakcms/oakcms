<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

Yii::setAlias('@adminTemplate', realpath(__DIR__ . '/../templates/backend/base'));
Yii::setAlias('@adminTemplate', realpath(__DIR__ . '/../templates/backend/base'));

Yii::setAlias('@media', realpath(__DIR__ . '/../media'));
$basePath =  dirname(__DIR__);
$webRoot = dirname($basePath);

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'OakCMS',
    'basePath' => dirname(__DIR__),
    'language' => 'en-US',
    'sourceLanguage' => 'en-US',
    'defaultRoute' => '',
    'runtimePath' => $webRoot . '/runtime',
    'vendorPath' => $webRoot . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'bootstrap' => [
        'log',
        'app\modules\system\Bootstrap',
        'app\modules\admin\Bootstrap',
        'app\modules\user\Bootstrap',
    ],
    'modules' => require(__DIR__ . '/modules.php'),
    'components' => require(__DIR__ . '/components.php'),
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['modules']['debug']['allowedIPs'] = ['*'];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'OakCMS' => Yii::getAlias('@adminTemplate/views/_gii/templates')
                ],
                'template' => 'OakCMS',
                'messageCategory' => 'oakcms'
            ],
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    'OakCMS' => Yii::getAlias('@adminTemplate/views/_gii/model')
                ],
                'template' => 'OakCMS',
                'messageCategory' => 'oakcms'
            ],
            'migrik' => [
                'class'     => \insolita\migrik\gii\StructureGenerator::class,
            ],
            'migrikdata'=>[
                'class'=>\insolita\migrik\gii\DataGenerator::class,
            ],
        ]
    ];
    //$config['components']['assetManager']['forceCopy'] = true;
}

return $config;
