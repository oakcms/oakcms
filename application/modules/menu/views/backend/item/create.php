<?php


/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\MenuItem */
/* @var $sourceModel app\modules\menu\models\MenuItem */
/* @var $linkParamsModel app\modules\menu\models\MenuLinkParams */

$this->title = Yii::t('menu', 'Add Menu Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-create">

    <?= $this->render('_form', [
        'model'           => $model,
        'sourceModel'     => $sourceModel,
        'linkParamsModel' => $linkParamsModel
    ]) ?>

</div>
