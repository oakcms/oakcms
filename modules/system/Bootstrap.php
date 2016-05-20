<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: Bootstrap.php
 */

namespace app\modules\system;

use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        /**
         *
         * @var Module $systemModule
         * @var \app\modules\user\Module $userModule
         *
         */
        if($app->hasModule('system') && ($systemModule = $app->getModule('system')) instanceof Module) {}
    }
}
