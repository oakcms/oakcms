<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 15.05.2016
 * Project: oakcms
 * File name: components.php
 */

return [
    'view' => [
        'class' => 'app\components\View',
    ],
    'request' => [
        'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),
        'baseUrl' => '',
    ],
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'user' => [
        'identityClass' => 'app\modules\user\models\User',
        'enableAutoLogin' => true,
        'loginUrl' => ['/admin/user/login'],
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
    'assetManager' => [
        'class' => 'yii\web\AssetManager',
        'linkAssets' => true,
        'appendTimestamp' => YII_ENV_DEV,
        'bundles' => [
            'zxbodya\yii2\tinymce\TinyMceAsset' => [
                'js' => [
                    '//cdn.tinymce.com/4/tinymce.min.js',
                    '//cdn.tinymce.com/4/jquery.tinymce.min.js'
                ],
            ]
        ],
        'converter' => [
            'class' => 'nizsheanez\assetConverter\Converter',
            'destinationDir' => 'css',
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
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            'db' => [
                'class' => 'yii\log\DbTarget',
                'levels' => ['error', 'warning'],
                'except' => ['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                'prefix' => function () {
                    $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                    return sprintf('[%s][%s]', Yii::$app->id, $url);
                },
                'logVars' => [],
                'logTable' => '{{%system_log}}'
            ]
        ],
    ],
    'i18n' => [
        'translations' => [
            '*' => [
                'class' => 'yii\i18n\DbMessageSource',
                'db' => 'db',
                'sourceLanguage' => 'en-US',
                'sourceMessageTable' => '{{%language_source}}',
                'messageTable' => '{{%language_translate}}',
                'cachingDuration' => 86400,
                'enableCaching' => true,
            ],
        ],
    ],
    'db' => require(__DIR__ . '/db.php'),
    'urlManager' => require(__DIR__ . '/urlManager.php'),
];
