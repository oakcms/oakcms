<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\text\models\Text;
 */

use yii\helpers\Url;
use app\modules\catalog\models\CatalogCategory;

if(count($model->getSetting('category')) > 0):
    $categoriesIds = (array)$model->getSetting('category');
    array_splice($categoriesIds, 4);
    $categories = CatalogCategory::find()->where(['id' => $categoriesIds])->all();

    if(count($categories) > 0):
?>
        <div class="category_link_more">
            <h3><?= $model->getSetting('title') ?></h3>
            <div class="list-last-news">
                <div class="container-fluid inline-layout col-2">
                    <?php foreach ($categories as $category): ?>
                        <?php /** @var $category CatalogCategory */?>
                        <a href="<?= Url::to($category->getFrontendViewLink()) ?>" class="item">
                            <div class="row-table">
                                <div class="td-row"><span><?= $category->title ?></span></div>
                            </div>
                        </a>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
