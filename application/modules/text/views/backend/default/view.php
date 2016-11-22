<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\text\models\Text */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('carousel', 'Texts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="text-view">

    <p>
        <?php echo Html::a(Yii::t('carousel', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('carousel', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('carousel', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'subtitle',
            'layout',
            'slug',
            'text:ntext',
            'status',
        ],
    ]) ?>

</div>
