<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \app\modules\shop\models\category\CategorySearch $searchModel
 * @var string $route
 */
$asset = \app\templates\backend\base\assets\BaseAsset::register($this);
$this->title = Yii::t('content', 'Select Category');
$this->params['breadcrumbs'][] = $this->title;

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


<?php echo app\modules\admin\widgets\ButtonDropdown::widget($langueBtn);
?>
<div class="page-index">
    <?php \yii\widgets\Pjax::begin(['id' => 'article-pjax']) ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
        'columns' => [
            [
                'attribute' => 'id',
                'options' => ['style' => 'width:100px']
            ],
            'title',
            'slug',
            [
                'header' => Yii::t('admin', 'Action'),
                'value' => function ($model) use ($route) {
                    /** @var $model \app\modules\content\models\ContentCategory */
                    return Html::a(Yii::t('admin', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \app\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('content', 'category: {title}', ['title' => $model->title]),
                            'route' => \app\modules\menu\models\MenuItem::toRoute($route, ['slug'=> $model->slug]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            //'value' => $model->category->slug . ':' . $model->slug
                        ]),
                    ]);
                },
                'options' => ['style' => 'width:80px'],
                'format' => 'raw'
            ]
        ]
	]) ?>
    <?php \yii\widgets\Pjax::end() ?>
</div>
