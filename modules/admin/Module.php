<?php

namespace app\modules\admin;

use yii\filters\AccessControl;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @var string The prefix for user module URL.
     *
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = 'admin';

    /** @var array The rules to be used in URL management. */
    public $urlRules = [
        '<action:(login|logout|signup|email-confirm|request-password-reset|password-reset)>' => 'default/<action>',
    ];

    public function behaviors()
    {
       return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'actions' => ['login'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::$app->set('view', [
            'class' => 'app\components\AdminView',
            'title' => 'Admin Template',
            'theme' => [
                'basePath' => '@app/templates/backend/base',
                'baseUrl' => '@web/templates/backend/base/web',
                'pathMap' => [
                    '@app/views' => '@app/templates/backend/base/views',
                    '@app/modules' => '@app/templates/backend/base/views/modules',
                    '@app/widgets' => '@app/templates/backend/base/views/widgets'
                ],
            ]
        ]);
        // custom initialization code goes here
    }
}
