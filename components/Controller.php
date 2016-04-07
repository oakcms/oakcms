<?php

namespace app\components;

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 08.04.2016
 * Project: oakcms
 * File name: Controller.php
 */
class Controller extends \yii\web\Controller
{

    public function back()
    {
        return $this->redirect(\Yii::$app->request->referrer);
    }

}