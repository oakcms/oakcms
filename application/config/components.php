<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 15.05.2016
 * Project: oakcms
 * File name: components.php
 */

return [
    'keyStorage'   => [
        'class' => 'app\components\KeyStorage',
    ],
    'request'      => [
        'class'               => 'app\components\Request',
        'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),
        'baseUrl'             => getenv('BASE_URL'),
        'parsers'             => [
            'application/json' => 'yii\web\JsonParser',
        ],
    ],
    'authManager'  => [
        'class' => 'yii\rbac\DbManager',
    ],
    'cache'        => [
        'class' => 'yii\caching\FileCache',
    ],
    'user'         => [
        'identityClass'   => 'app\components\UserIdentity',
        'enableAutoLogin' => true,
        'loginUrl'        => ['admin/user/login'],
    ],
    'errorHandler' => [
        'errorAction'       => 'system/default/error',
        'exceptionView'     => '@app/views/layouts/exception.php',
        'callStackItemView' => '@app/views/layouts/callStackItem.php',
    ],
    'view'         => [
        'class'            => 'app\components\CoreView',
        'enableMinify'     => false,
        'concatCss'        => true,                                     // concatenate css
        'minifyCss'        => true,                                     // minificate css
        'concatJs'         => true,                                     // concatenate js
        'minifyJs'         => true,                                     // minificate js
        'minifyOutput'     => true,                                     // minificate result html page
        'web_path'         => '@web',                                   // path alias to web base
        'base_path'        => '@webroot',                               // path alias to web base
        'minify_path'      => '@webroot/assets',                        // path alias to save minify result
        'js_position'      => [\yii\web\View::POS_END],                // positions of js files to be minified
        'force_charset'    => 'UTF-8',                                  // charset forcibly assign, otherwise will use all of the files found charset
        'expand_imports'   => true,                                     // whether to change @import on content
        'compress_options' => ['extra' => true],                        // options for compress
        'excludeBundles'   => [
            #\dev\hellowrld\AssetBundle::class, // exclude this bundle from minification
        ],
        'as seo'           => [
            'class' => 'app\modules\system\components\SeoViewBehavior',
        ],
    ],
    'mailer'       => [
        'class'            => 'yii\swiftmailer\Mailer',

        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        // 'useFileTransport' => true,
    ],
    'assetManager' => [
        'class'           => 'yii\web\AssetManager',
        'linkAssets'      => false,
        'appendTimestamp' => YII_ENV_DEV,
        'converter'       => [
            'class'          => 'nizsheanez\assetConverter\Converter',
            'destinationDir' => 'css',
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
    ],
    'log'          => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets'    => [
            'db' => [
                'class'    => 'yii\log\DbTarget',
                'levels'   => ['error', 'warning'],
                'except'   => ['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                'prefix'   => function () {
                    $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;

                    return sprintf('[%s][%s]', Yii::$app->id, $url);
                },
                'logVars'  => [],
                'logTable' => '{{%system_log}}',
            ],
        ],
    ],
    'i18n'         => [
        'translations' => [
            '*' => [
                'class'              => 'yii\i18n\DbMessageSource',
                'db'                 => 'db',
                'sourceLanguage'     => 'en-US',
                'sourceMessageTable' => '{{%language_source}}',
                'messageTable'       => '{{%language_translate}}',
                'cachingDuration'    => 86400,
                'enableCaching'      => false,
            ],
        ],
    ],
    'opengraph' => [
        'class' => 'dragonjet\opengraph\OpenGraph',
    ],
    'db'           => require(__DIR__ . '/db.php'),
    'urlManager'   => require(__DIR__ . '/urlManager.php'),
];
