<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentPages */

$this->title = Yii::t('content', 'Create Content Pages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'Content Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-pages-create">

    <?= $this->render('_form', [
        'model' => $model,
        'lang' => $lang,
    ]) ?>

</div>
