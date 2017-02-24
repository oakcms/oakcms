<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\system\models\SystemBackCall */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'System Back Calls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-back-call-view">

    <p>
        <?php echo Html::a(Yii::t('system', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('system', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('system', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'email:email',
            'phone',
            'created_at',
            'status',
        ],
    ]) ?>

</div>
