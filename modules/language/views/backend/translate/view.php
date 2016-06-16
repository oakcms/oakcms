<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\language\models\LanguageTranslate */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Language Translates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-translate-view">

    <p>
        <?php echo Html::a(Yii::t('language', 'Update'), ['update', 'id' => $model->id, 'language' => $model->language], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('language', 'Delete'), ['delete', 'id' => $model->id, 'language' => $model->language], [
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
            'id',
            'language',
            'translation:ntext',
        ],
    ]) ?>

</div>
