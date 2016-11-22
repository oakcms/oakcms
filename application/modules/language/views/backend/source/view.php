<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\modules\admin\widgets\Button;

/* @var $this yii\web\View */
/* @var $model app\modules\language\models\LanguageSource */

$this->title = $model->message;
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Language Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'label' => Yii::t('admin', 'Update'),
        'tagName' => 'a',
        'options' => [
            'href' => Url::to(['update', 'id' => $model->id])
        ],
        //'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-default'
    ],[
        'label' => Yii::t('language', 'Create translation'),
        'tagName' => 'a',
        'options' => [
            'href' => Url::to(['/admin/language/translate/create', 'id' => $model->id])
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-primary'
    ],[
        'label' => Yii::t('admin', 'Delete'),
        'tagName' => 'a',
        'options' => [
            'href' => Url::to(['delete', 'id' => $model->id]),
            'data' => [
                'confirm' => Yii::t('admin', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ],
        'icon' => 'glyphicon glyphicon-trash',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-danger'
    ],
];
?>
<div class="language-source-view">

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category',
            'message:ntext',
        ],
    ]) ?>

</div>
