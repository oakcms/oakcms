<?php

namespace app\modules\admin;

use app\components\View;
use Yii;
use app\modules\admin\rbac\Rbac;
use yii\base\Application;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;

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
                        'roles' => [Rbac::PERMISSION_ADMIN_PANEL],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        //VarDumper::dump($action, 10, true);

        \Yii::$app->set('view', [
            'class' => 'app\components\AdminView',
            'title' => 'Admin Template',
            'theme' => [
                'basePath'  => '@app/templates/backend/base',
                'baseUrl'   => '@web/templates/backend/base/web',
                'pathMap'   => [
                    '@app/views'    => '@app/templates/backend/base/views',
                    '@app/modules'  => '@app/templates/backend/base/views/modules',
                    '@app/widgets'  => '@app/templates/backend/base/views/widgets'
                ],
            ]
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();
    }
}
