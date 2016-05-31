<?php

namespace app\modules\admin;

use Yii;
use yii\base\Application;
use yii\filters\AccessControl;
use app\components\View;
use app\modules\admin\rbac\Rbac;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{

    public $activeModules;

    public $menuSidebar = [];


    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @var string The prefix for user module URL.
     *
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = '/admin';


    /** @var array The rules to be used in URL management. */
    public $urlRules = [
        ''                                                                  => 'default/index',
        'user/<action:[\w\-]+>'                                             => 'user/user/<action>',
        '<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>'                    => '<controller>/<action>',
        '<controller:[\w\-]+>/<action:[\w\-]+>'                             => '<controller>/<action>',
        '<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>'   => '<module>/<controller>/<action>',
        '<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>'            => '<module>/<controller>/<action>',
        '<module:[\w\-]+>/<controller:[\w\-]+>'                             => '<module>/<controller>/index',
        '<module:[\w\-]+>'                                                  => '<module>/default/index',
        '<module:[\w\-]+>/<controller:[\w\-]+>/<id:\d+>'                    => '<module>/<controller>/view',
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

        return true;
    }

    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();

        if(Yii::$app->cache === null) {
            throw new \yii\web\ServerErrorHttpException('Please configure Cache component.');
        }

        if (Yii::$app instanceof \yii\web\Application) {
            if (!defined('LIVE_EDIT')) define('LIVE_EDIT', !Yii::$app->user->isGuest && Yii::$app->session->get('oak_live_edit'));
        }
    }
}
