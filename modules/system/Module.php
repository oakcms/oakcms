<?php

namespace app\modules\system;

use Yii;
use yii\base\Application;
use app\components\View;

/**
 * system module definition class
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\system\controllers';


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

        define('LIVE_EDIT', !Yii::$app->user->isGuest && Yii::$app->session->get('oak_live_edit'));

        \Yii::$app->set('view', [
            'class' => 'app\components\View',
            'title' => 'Frontend Template',
            'theme' => [
                'basePath' => '@app/templates/frontend/base',
                'baseUrl' => '@web/templates/frontend/base/web',
                'pathMap' => [
                    '@app/views' => '@app/templates/frontend/base/views',
                    '@app/modules' => '@app/templates/frontend/base/views/modules',
                    '@app/widgets' => '@app/templates/frontend/base/views/widgets'
                ],
            ],
        ]);

        if(!Yii::$app->user->isGuest && strpos(Yii::$app->request->pathInfo, 'admin') === false) {
            Yii::$app->on(Application::EVENT_BEFORE_REQUEST, function () {
                Yii::$app->getView()->bodyClass[] = 'oak-admin-bar';
                Yii::$app->getView()->on(View::EVENT_END_BODY, [$this, 'renderToolbar']);
            });
        }
    }

    public function renderToolbar()
    {
        $view = Yii::$app->getView();
        echo $view->render('@app/modules/system/views/layouts/blocks/admin_bar.php');
    }
}
