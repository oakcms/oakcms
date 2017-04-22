<?php
/**
 * @var $model \app\modules\text\models\Text;
 */

use yii\helpers\Html;
use app\modules\catalog\models\CatalogCategory;
use app\modules\catalog\models\CatalogCategoryLang;

$categories = \app\modules\catalog\models\CatalogCategory::find()
    ->joinWith(['translations'])
    ->where([
        CatalogCategory::tableName() . '.status'       => CatalogCategory::STATUS_PUBLISHED,
        CatalogCategory::tableName() . '.show_home'    => CatalogCategory::STATUS_PUBLISHED,
        CatalogCategoryLang::tableName() . '.language' => Yii::$app->language,
    ])
    ->all();
?>

<div class="block_category_link wow fadeIn <?= $model->getSetting('cssClass') ?>">
    <div class="container">
        <h2><?= $model->getSetting('title') ?></h2>
        <div class="list_category_link">
            <?php foreach ($categories as $category): ?>
            <div class="item_category">
                <?php /** @var $category CatalogCategory */ ?>
                <h4><?= Html::a($category->title, $category->getFrontendViewLink()) ?></h4>
                <div class="list_subcategory">
                    <?php foreach ($category->items as $item): ?>
                    <?php /** @var $item \app\modules\catalog\models\CatalogItems */ ?>
                    <p class="item"><?= Html::a($item->title, $item->getFrontendViewLink()) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
