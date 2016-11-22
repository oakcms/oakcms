<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentPages */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'Content Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-pages-view">

    <p>
        <?php echo Html::a(Yii::t('content', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('content', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('content', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'layout',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
