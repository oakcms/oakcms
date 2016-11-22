<?php

namespace app\modules\user;

use rmrevin\yii\minify\HtmlCompressor;
use rmrevin\yii\minify\View;
use yii\base\BootstrapInterface;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $time = time();

        $js = 'var autoTimer, autoInterval;
            window.onload       = resetTimer;
            window.onmousemove  = resetTimer;
            window.onmousedown  = resetTimer;
            window.onclick      = resetTimer;
            window.onscroll     = resetTimer;
            window.onkeypress   = resetTimer;
    
            function lockScreen() {
                setInterval(function () {
                    clearInterval(autoInterval);
                    if(typeof $.cookie(\'LockScreenSession\') == "undefined") {
                        window.location.href = \''.Url::to(['/admin/user/user/lock-screen']).'\'
                    }
                }, 3000);
            }
    
            function resetTimer() {
                clearTimeout(autoTimer);
                autoTimer = setTimeout(lockScreen, 1000 * 60 * 15);
    
            }';

        if (!\Yii::$app->user->isGuest) {
            \Yii::$app->getView()->registerJs(HtmlCompressor::compress($js, ['extra'=>true]), View::POS_HEAD);
            \Yii::$app->getView()->registerJsFile(\Yii::getAlias('@web/application/media/js/jquery.cookie.js'), ['depends' => 'yii\web\JqueryAsset'], 'jquery.cookie.js');

            unset($_COOKIE["LockScreenSession"]);
            setcookie("LockScreenSession", $time, $time + 60 * 15, '/');
        }
    }
}
