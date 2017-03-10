<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\form_builder\models\FormBuilderForms */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('form_builder', 'Form Builder Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-builder-forms-view">

    <p>
        <?php echo Html::a(Yii::t('form_builder', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('form_builder', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('form_builder', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'slug',
            'sort',
            'status',
            'data:ntext',
        ],
    ]) ?>

</div>
