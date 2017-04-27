<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\MenuItem */
/* @var $linkParamsModel app\modules\menu\models\MenuLinkParams */

$this->title = Yii::t('menu', 'Update Menu Item: {title}', [
    'title' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->menuType->title, 'url' => ['index', 'MenuItemSearch' => ['menu_type_id' => $model->menuType->id]]];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('menu', 'Update');
?>
<div class="menu-update">

    <?= $this->render('_form', [
        'model'           => $model,
        'linkParamsModel' => $linkParamsModel
    ]) ?>

</div>
