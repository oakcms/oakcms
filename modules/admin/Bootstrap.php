<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: Bootstrap.php
 */

namespace app\modules\admin;

use Yii;
use app\components\View;
use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        /**
         *
         * @var Module $adminModule
         * @var \app\modules\user\Module $userModule
         *
         */

        if (
            ($app->hasModule('user') && ($userModule = $app->getModule('user')) instanceof \app\modules\user\Module) &&
            ($app->hasModule('admin') && ($adminModule = $app->getModule('admin')) instanceof Module)
        ) {

            Yii::setAlias('admin', '@app/modules/admin');

            Yii::$container->set('yii\web\User', [
                'enableAutoLogin' => true,
                'loginUrl'        => ['/admin/user/login'],
                'identityClass'   => $userModule->modelMap['User'],
            ]);

            $configUrlRule = [
                'prefix' => $adminModule->urlPrefix,
                'rules'  => $adminModule->urlRules,
            ];

            if ($adminModule->urlPrefix != 'admin') {
                $configUrlRule['routePrefix'] = 'admin';
            }

            $configUrlRule['class'] = 'yii\web\GroupUrlRule';
            $rule = Yii::createObject($configUrlRule);

            $app->urlManager->addRules([$rule], false);

            if (!Yii::$app->user->isGuest && strpos(Yii::$app->request->pathInfo, 'admin') === false && strpos(Yii::$app->request->pathInfo, 'gii') === false) {
                $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
                    Yii::$app->getView()->bodyClass[] = 'oak-admin-bar';
                    $app->getView()->on(View::EVENT_BEGIN_BODY, [$this, 'renderToolbar']);
                });
            }
        }
    }
    public function renderToolbar()
    {
        $view = Yii::$app->getView();
        echo $view->render('@app/modules/admin/views/layouts/blocks/admin_bar.php');
    }
}
