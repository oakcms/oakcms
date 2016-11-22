<?php

namespace app\modules\system;

use app\modules\admin\models\ModulesModules;
use Yii;
use yii\base\Application;
use app\components\FrontendView;
use yii\helpers\VarDumper;

/**
 * system module definition class
 */
class Module extends \yii\base\Module
{

    const VERSION = '0.0.1';

    public $activeModules;

    public $settings = [
        'BackCallEmail' => [
            'type' => 'textInput',
            'value' => 'script@email.ua'
        ],
        'BackCallSubject' => [
            'type' => 'textInput',
            'value' => 'Новая заявка з сайта falconcity.kz'
        ],
        'BackCallSuccessText' => [
            'type' => 'textInput',
            'value' => 'Ваш запрос получен!<br>В ближайшее время наш менеджер свяжится с Вами!'
        ],
        'SocialInstagramLink' => [
            'type' => 'textInput',
            'value' => '#'
        ],
        'SocialTwitterLink' => [
            'type' => 'textInput',
            'value' => '#'
        ],
        'SocialFacebookLink' => [
            'type' => 'textInput',
            'value' => '#'
        ],
        'FrequentlyAskedQuestionsLink' => [
            'type' => 'textInput',
            'value' => '#'
        ],
    ];

    public static $urlRulesFrontend = [
        '<action:(?!admin|user)(.*)+>/<id:\d+>'        => 'system/default/<action>',
        '<action:(?!admin|user)(.*)+>/page-<page:\d+>' => 'system/default/<action>',
        '<action:(?!admin|user)(.*)+>'                 => 'system/default/<action>',
    ];

    public static $installConfig = [];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\system\controllers\frontend';

    /**
     * Функція яка повертає варсію системи:
     *
     * @return string
     **/
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Повертає копірайт:
     *
     * @return string
     **/
    public static function powered()
    {
        return \Yii::t('yii', 'Powered by {OakCMS}', [
            'OakCMS' => '<a href="http://www.oakcms.com/" rel="external">' . \Yii::t('yii', 'OakCMS') . '</a>'
        ]);
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

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
            'expand_imports' => false,
            'compress_output' => false,
            'compress_options' => ['extra' => false],
            'concatCss' => false,
            'minifyCss' => false,
            'concatJs' => false,
            'minifyJs' => false,

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
            'appendTimestamp' => YII_ENV_DEV,
            'forceCopy' => YII_ENV_DEV,
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
