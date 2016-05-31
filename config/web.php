<?php

Yii::setAlias('@adminTemplate', realpath(__DIR__ . '/../templates/backend/base'));

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'OakCMS',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'defaultRoute' => 'system/default/index',
    'bootstrap' => [
        'log',
        'app\modules\admin\Bootstrap',
        'app\modules\system\Bootstrap',
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
            ]
        ]
    ];
}

return $config;
