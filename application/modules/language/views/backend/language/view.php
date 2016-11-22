<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\language\models\Language */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-view">

    <p>
        <?php echo Html::a(Yii::t('language', 'Update'), ['update', 'id' => $model->language_id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('language', 'Delete'), ['delete', 'id' => $model->language_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('language', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'language_id',
            'language',
            'country',
            'url:url',
            'name',
            'name_ascii',
            'status',
        ],
    ]) ?>

</div>
