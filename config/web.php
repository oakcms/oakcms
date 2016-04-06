<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'OakCMS',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'system/default/index',
    'bootstrap' => [
        'log',
        'app\modules\admin\Bootstrap'
    ],
    'modules' => [
        'system' => [
            'class' => 'app\modules\system\Module',
        ],
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'modules' => [
                'users' => [
                    'class' => 'app\modules\user\Module',
                    'controllerNamespace' => 'app\modules\user\controllers\backend',
                    'viewPath' => '@app/modules/user/views/backend',
                ],
            ]
        ],
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
    ],
    'components' => [
        'view' => [
            'theme' => [
                'basePath' => '@app/templates/frontend/base',
                'baseUrl' => '@web/templates/frontend/base/web',
                'pathMap' => [
                    '@app/views' => '@app/templates/frontend/base/views',
                    '@app/modules' => '@app/templates/frontend/base/views/modules',
                    '@app/widgets' => '@app/templates/frontend/base/views/widgets'
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),
            'baseUrl'=> '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl'        => ['/admin/default/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'system/default/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
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
                    'logTable'=>'{{%system_log}}'
                ]
            ],
        ],
        'i18n' => [
            'translations' => [
                '*'=> [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable'=>'{{%i18n_source_message}}',
                    'messageTable'=>'{{%i18n_message}}',
                    'enableCaching' => true,
                    'cachingDuration' => 900
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => require(__DIR__ . '/urlManager.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
