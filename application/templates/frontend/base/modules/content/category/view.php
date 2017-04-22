<?php
/**
 * @var $breadcrumbs array
 * @var $this \app\components\View
 * @var $model \app\modules\content\models\ContentCategory
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
?>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => ['class' => 'item'],
    'itemView' => '_item',
    'layout' => "<div class='block_news'><div class='list_news inline-layout col-3'>{items}</div></div>\n<div class='list_pagination'>{pager}</div>",
    'pager' => [
        'registerLinkTags' => true,
        'options' => [
            'class' => 'inline-layout'
        ]
    ]
]); ?>
