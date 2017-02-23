<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\text\models\Text */

$this->title = Yii::t('carousel', 'Create Text');
$this->params['breadcrumbs'][] = ['label' => Yii::t('carousel', 'Texts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="text-create">

    <?= $this->render('_form', [
        'model' => $model,
        'lang'  => $lang,
        'layouts' => $layouts,
        'positions' => $positions,
        'menus' => $menus
    ]) ?>

</div>
