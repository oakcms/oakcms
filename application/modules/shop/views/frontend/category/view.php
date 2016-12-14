<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.12.2016
 * Project: oakcms
 * File name: view.php
 *
 * @var $model \app\modules\shop\models\Category;
 */

use app\modules\admin\widgets\Html;

echo Html::tag('h1', $model->name);

$activeMenu = Yii::$app->menuManager->activeMenu;
var_dump($activeMenu);
//\yii\helpers\VarDumper::dump(Yii::$app->getUrlManager()->rules, 10, true);
