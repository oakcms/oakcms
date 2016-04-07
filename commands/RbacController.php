<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\modules\admin\rbac\Rbac;
use app\modules\user\models\User;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $user = $auth->createRole(User::ROLE_USER);
        $auth->add($user);

        $manager = $auth->createRole(User::ROLE_MANAGER);
        $auth->add($manager);
        $auth->addChild($manager, $user);

        $loginToBackend = $auth->createPermission(Rbac::PERMISSION_ADMIN_PANEL);
        $auth->add($loginToBackend);
        $auth->addChild($manager, $loginToBackend);

        $admin = $auth->createRole(User::ROLE_ADMINISTRATOR);
        $auth->add($admin);
        $auth->addChild($admin, $manager);

        $auth->assign($admin, 1);

        Console::output('Done!');
    }
}
