<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentArticles */

$this->title = Yii::t('admin', 'Create Content Articles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Content Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-articles-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
