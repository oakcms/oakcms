<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\seo\models\SeoItems */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('seo', 'Seo Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seo-items-view">

    <p>
        <?php echo Html::a(Yii::t('seo', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('seo', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('seo', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'link',
            'title',
            'keywords:ntext',
            'description:ntext',
            'canonical',
            'status',
        ],
    ]) ?>

</div>
