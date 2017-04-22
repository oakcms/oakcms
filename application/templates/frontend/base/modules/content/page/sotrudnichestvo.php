<?php
/**
 * @var $model \app\modules\content\models\ContentPages
 * @var $this \app\components\CoreView
 */

$this->bodyClass = ['page-'.$model->id];

/** @var \app\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {

    $this->pageTitle = $model->title;
    $this->pageTitleHeading = 'h1';
    $this->title = $model->title;
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs(false);
}

$meta_title = ($model->meta_title != '') ? $model->meta_title : $model->title;
$meta_description = \app\helpers\StringHelper::truncate((($model->meta_description != '') ? $model->meta_description : strip_tags($model->description)), '140', '');
$meta_keywords = ($model->meta_keywords != '') ? $model->meta_keywords : implode(', ', explode(' ', $model->title));

$this->setSeoData($meta_title, $meta_description, $meta_keywords);

?>
<article class="block_sotrudnichestvo <?= $model->slug ?>">
    <?= $model->content ?>
</article>
