<?php


/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\MenuType */

$this->title = Yii::t('gromver.platform', 'Add Menu Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Menu Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
