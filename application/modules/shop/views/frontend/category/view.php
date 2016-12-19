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

use yii\helpers\Url;
use yii\helpers\Html;

echo Html::tag('h1', $model->name);

echo \yii\bootstrap\Html::a('link 1', ['/shop/category/view', 'slug' => 'kategoria-1']);echo " | ";
echo \yii\bootstrap\Html::a('link 2', ['/shop/category/view', 'slug' => 'komp-uternie-stoly']);echo " | ";
echo \yii\bootstrap\Html::a('link 3', ['/shop/category/view', 'slug' => '12']); echo " | ";
echo \yii\bootstrap\Html::a('link 4', ['/shop/category/view', 'slug' => 'kategoria-2']);echo " | ";
