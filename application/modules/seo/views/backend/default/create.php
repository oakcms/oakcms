<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\seo\models\SeoItems */

$this->title = Yii::t('seo', 'Create Seo Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('seo', 'Seo Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seo-items-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
