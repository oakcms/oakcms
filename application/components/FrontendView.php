<?php

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: View.php
 */

namespace app\components;

use app\modules\admin\widgets\Html;
use yii\helpers\Url;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;

class FrontendView extends CoreView
{
    public $bodyClass = [];
    public $adminPanel = true;

    public function init()
    {
        parent::init();
    }
}
