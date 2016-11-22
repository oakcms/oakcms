<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentCategory */

$this->title = Yii::t('content', 'Create Content Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'Content Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
