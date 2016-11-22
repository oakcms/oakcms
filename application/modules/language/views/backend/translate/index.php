<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;
use app\modules\language\models\LanguageTranslate;

/**
 * @var $this yii\web\View
 * @var $searchModel app\modules\language\models\search\LanguageTranslateSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $categories
 */

$this->title = Yii::t('language', 'Language Translates');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName' => 'a',
        'label' => Yii::t('admin', 'Create'),
        'options' => [
            'href' => Url::to(['create'])
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
    ],
];
?>
<div class="language-translate-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'id' => 'grid',
            'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'options' => ['style' => 'width:100px']
                ],
                [
                    'attribute' => 'language',
                    'value' => function($model) {
                        return \app\modules\language\models\Language::findOne($model->language)->name;
                    },
                    'options' => ['style' => 'width: 120px']
                ],
                [
                    'attribute' => 'category',
                    'filter'    => $categories
                ],
                [
                    'attribute' => 'sourceMessage',
                ],
                'translation:ntext',
                ['class' => 'app\modules\admin\components\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
