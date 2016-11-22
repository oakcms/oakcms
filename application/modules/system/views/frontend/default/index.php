<?php

/**
 * @var $this \app\components\View;
 * @var $dataProviderCatalog \app\modules\catalog\models\CatalogItems;
 */

use \yii\helpers\Url;
use app\modules\text\api\Text;

$this->setSeoData('Osnova', '', '', '/');

$bundle = \app\templates\frontend\base\assets\BaseAsset::register($this);

$this->registerJsFile('//maps.googleapis.com/maps/api/js?key=AIzaSyA9aTGWSRFbk4Fw6mUjDmTUPnsj5Imves0', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('var mapCoordinates = "'.\yii\helpers\Url::to(['/realestate/category/get-response-positions']).'";', \yii\web\View::POS_HEAD, 'mapCoordinates');
$this->registerJsFile($bundle->baseUrl.'/js/markerclusterer.js', ['position' => \yii\web\View::POS_END], 'markerclusterer');
$this->registerJsFile($bundle->baseUrl.'/js/map_category.js', ['position' => \yii\web\View::POS_END], 'mapCoordinates');

$categories = \app\modules\realestate\models\RealEstateCategory::find()
    ->published()
    ->all();
?>
<section class="main-objects-container objects-container">
    <section class="main-about-container" id="main-about">
        <?php echo Text::get('main_about'); ?>
    </section>
    <section class="objects-container">
        <div class="container">
            <h2 class="h2-red"><?= Yii::t('system', 'All properties') ?></h2>
        </div>
        <div class="line-block">
            <div class="container">
                <div class="line-block__right">
                    <p><?= Yii::t('system', 'Click over the object icon for details') ?></p>
                </div>
            </div>
        </div>
        <div class="map-container">
            <div id="map" data-baseurl="<?= $bundle->baseUrl ?>"></div>
        </div>
        <div class="objects-map-container">
            <div class="container">
                <div class="objects-map row clearfix">
                    <div class="objects-map col-sm-6">
                        <div class="objects-map__item">
                            <div class="objects-map__item-icon">
                                <img src="<?= $bundle->baseUrl ?>/img/map-icon/blue-big.png" alt="">
                            </div>
                            <div class="objects-map__item-text">
                                <p><?= Yii::t('system', 'The area to be less than 10 objects') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="objects-map col-sm-6">
                        <div class="objects-map__item">
                            <div class="objects-map__item-icon">
                                <img src="<?= $bundle->baseUrl ?>/img/map-icon/red-big.png" alt="">
                            </div>
                            <div class="objects-map__item-text">
                                <p><?= Yii::t('system', 'The area to be more than 10 objects')?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="objects">
            <div class="container">
                <div class="objects__list">
                    <div class="objects row clearfix">
                        <?foreach ($categories as $category):?>
                        <div class="objects__col col-xs-2 wow fadeInRight" data-wow-offset="10" data-wow-duration="1s">
                            <div class="objects-item">
                                <div class="objects-item__icon">
                                    <img src="<?= $bundle->baseUrl.'/'.$category->markerType ?>" alt="">
                                </div>
                                <div class="objects-item__text">
                                    <p><?= $category->title ?></p>
                                    <a href="<?= Url::to(['/realestate/category/view', 'slug' => $category->slug]) ?>">
                                        <?= Yii::t('realestate', '{count} view all', ['count' => $category->countItems]) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?endforeach;?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<?php echo Text::get('news_index'); ?>

<?php echo Text::get('we_trust'); ?>
