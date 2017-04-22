<?php
/**
 * @var $breadcrumbs array
 * @var $this \app\components\View
 * @var $model \app\modules\content\models\ContentCategory
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\widgets\ListView;

$this->pageTitle = $model->title;

/** @var \app\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('content', 'News');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs(false);
} else {
    $this->title = Yii::t('content', 'News');
}

$this->setSeoData(($model->meta_title != '' ? $model->meta_title:$model->title), $model->meta_description, $model->meta_keywords);
$dataProvider->setPagination(['pageSize' => 9]);
?>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => ['class' => 'item'],
    'itemView' => '_item_customer_testimonials',
    'layout' => "<div class='block_feedback'>" . $model->content . "<div class='list_feedback inline-layout col-3'>{items}</div></div>\n<div class='list_pagination'>{pager}</div>",
    'pager' => [
        'registerLinkTags' => true,
        'options' => [
            'class' => 'inline-layout'
        ],
        'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
        'prevPageLabel' => '<i class="fa fa-angle-right"></i>'
    ]
]); ?>
