<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \app\modules\content\models\search\ContentPagesSearch $searchModel
 * @var string $route
 */


$this->title = Yii::t('content', 'Select Page');
$this->params['breadcrumbs'][] = $this->title;

$asset = \app\templates\backend\base\assets\BaseAsset::register($this);

$allLang = \app\modules\language\models\Language::getLanguages();
$langueBtnItems = [];
foreach($allLang as $item) {
    if($lang->language_id != $item->language_id) {
        $langueBtnItems[] = [
            'label' => '<img src="'.$asset->baseUrl.'/images/flags/'.$item->url.'.png" alt="'.$item->url.'"/> '.$item->name,
            'url' => ['', 'language' => $item->url]
        ];
    }
}

$langueBtn = [
    'label' => '<img src="'.$asset->baseUrl.'/images/flags/'.$lang->url.'.png" alt="'.$lang->url.'"/> '.$lang->name,
    'options' => [
        'class' => 'btn blue btn-outline btn-circle btn-sm',
        'data-hover'=>"dropdown",
        'data-close-others'=>"true",
    ],
    'encodeLabel' => false,
    'dropdown' => [
        'encodeLabels' => false,
        'options' => ['class' => 'pull-left'],
        'items' => $langueBtnItems,
    ],
];
?>
<div class="page-index">
    <?php echo app\modules\admin\widgets\ButtonDropdown::widget($langueBtn);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
        'id' => 'grid',
        'columns' => [
            [
                'attribute' => 'id',
                'options' => ['width' => '60px'],
            ],
            [
                'attribute' => 'title',
                'value'     => function ($model) {
                    /** @var $model \app\modules\content\models\ContentPages */
                    return str_repeat("&ndash;&nbsp; ", max($model->level - 1, 0)) . $model->title;
                },
                'format'    => 'raw',
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var $model \app\modules\content\models\ContentPages */
                    return $model->getStatusLabel();
                },
                'options' => ['width' => '100px'],
                'filter' => \app\modules\content\models\ContentPages::statusLabels()
            ],
            [
                'header' => Yii::t('menu', 'Action'),
                'value' => function ($model) use ($route) {
                    /** @var $model \app\modules\content\models\ContentPages */
                    return Html::a(Yii::t('menu', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \app\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('menu', 'Page: {title}', ['title' => $model->title]),
                            'route' => \app\modules\menu\models\MenuItem::toRoute($route, ['slug' => $model->slug]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'value' => $model->id . ':' . $model->slug
                        ]),
                    ]);
                },
                'options' => ['width' => '80px'],
                'format' => 'raw'
            ]
        ],
    ]) ?>

</div>
