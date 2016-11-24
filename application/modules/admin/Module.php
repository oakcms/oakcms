<?php

namespace app\modules\admin;

use yii\helpers\Url;
use Yii;
use yii\filters\AccessControl;
use app\modules\admin\rbac\Rbac;

/**
 * admin module definition class
 */
class Module extends \app\components\module\Module
{

    public $activeModules;

    public $menuSidebar = [];


    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public $viewPath = '@app/modules/admin/views/backend';

    /**
     * @var string The prefix for user module URL.
     *
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = '/admin';


    /** @var array The rules to be used in URL management. */
    public $urlRules = [
        ''                                                                      => 'default/index',
        'user/<_a:[\w\-]+>'                                                     => 'user/user/<_a>',
        '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>'                                    => '<_c>/<_a>',
        '<_c:[\w\-]+>/<_a:[\w\-]+>'                                             => '<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>/<language:\w+>'        => '<_m>/<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>'                       => '<_m>/<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>'                                => '<_m>/<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>'                                             => '<_m>/<_c>/index',
        '<_m:[\w\-]+>'                                                          => '<_m>/default/index',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<id:\d+>'                                    => '<_m>/<_c>/view',
    ];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Rbac::PERMISSION_ADMIN_PANEL],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
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

        $rHostInfo = Url::home(true);
        if (!Yii::$app->user->isGuest && strpos(Yii::$app->request->absoluteUrl, $rHostInfo.'admin') !== false) {
            \Yii::$app->view->theme->basePath = '@app/templates/backend/base';
            \Yii::$app->view->theme->baseUrl = '@web/templates/backend/base/web';
            \Yii::$app->view->theme->pathMap = [
                '@app/views' => '@app/templates/backend/base/views',
                '@app/modules' => '@app/templates/backend/base/views/modules',
                '@app/widgets' => '@app/templates/backend/base/views/widgets'
            ];
        }
    }

    public function getSettings($module) {
        if(isset(\Yii::$app->getModule('admin')->activeModules[$module]->settings)) {
            return \Yii::$app->getModule('admin')->activeModules[$module]->settings;
        } else {
            return [];
        }

    }
}
