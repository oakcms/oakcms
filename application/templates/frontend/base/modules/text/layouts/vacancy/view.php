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
use app\modules\catalog\models\CatalogItems;
use app\modules\catalog\models\CatalogItemsLang;

if(count($model->getSetting('items')) > 0):
$itemsIds = (array)$model->getSetting('items');
array_splice($itemsIds, 4);
$items = CatalogItems::find()
    ->joinWith(['translations'])
    ->where([
        CatalogItems::tableName().'.id' => $itemsIds,
        CatalogItems::tableName().'.status' => CatalogItems::STATUS_PUBLISHED,
        CatalogItemsLang::tableName().'.language' => Yii::$app->language
    ])
    ->all();

    if(count($items) > 0):
    ?>
        <div id="block_vacancy" class="block_vacancy">
            <div class="container inline-layout col-5">
                <?php foreach ($items as $item):?>
                    <?php /** @var $item CatalogItems */?>
                    <div class="item inline-layout">
                        <a href="<?= Url::to($item->getFrontendViewLink()) ?>" class="name">
                            <?= $item->title ?>
                        </a>
                        <div class="img-holder">
                            <img src="<?= $item->getImage()->getUrl('100') ?>" alt="<?= $item->getImage()->alt ?>">
                        </div>
                        <div class="block_text"><?= \yii\helpers\StringHelper::truncateWords($item->duties, 10) ?></div>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
