<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\seo\models\search\SeoItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('seo', 'Seo Items');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName' => 'a',
        'label' => Yii::t('seo', 'Create'),
        'options' => [
            'href' => Url::to(['create'])
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
    ]
]
?>
<div class="seo-items-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn'],
                'id',
                'link',
                'title',
                'keywords:ntext',
                'description:ntext',
                // 'canonical',
                // 'status',
                ['class' => 'app\modules\admin\components\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
