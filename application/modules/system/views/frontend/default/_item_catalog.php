<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.07.2016
 * Project: events.timhome.vn.loc
 * File name: _item_catalog.php
 *
 * @var $model \app\modules\catalog\models\CatalogItems;
 */
?>

<section class="container pre-gk">
    <div class="gk-gen-text">
        <div class="col-xs-6 gk-pre-img">
            <?php if(isset($model->main_photo)):?>
            <img src="<?= $model->main_photo ?>" alt="" class="img-responsive" />
            <?php endif?>
        </div>
        <div class="col-xs-6 gk-pre-text">
            <div class="gk-name"><?= $model->title ?></div>
            <div class="gk-address"><?= $model->address ?></div>
            <div class="gk-cost">
                <div class="gk-cost-m">
                    Từ <?= (float)$model->min_price_m2 ?> triệu VNĐ/m<sup>2</sup>
                </div>
                <div class="gk-cost-apart">Giá từ <?= (float)$model->min_price_flat ?> tỷ VNĐ/căn hộ</div>
            </div>
        </div>
    </div>
    <div class="gk-other">
        <div class="gk-anons">
            <div class="gk-anons-title"><?= $model->name_announcement ?></div>
            <div class="gk-anons-date"><?= Yii::$app->formatter->asDate($model->date_event, 'dd.MM.Y') ?></div>
        </div>
        <div class="gk-other-data">
            <div class="gk-counter">
                <div class="gk-counter-title">Đã quan tâm:</div>
                <div class="gk-counter-nuber">
                    <?= $model->hit ?> <span>người</span>
                </div>
            </div>
            <div class="dotted"></div>
            <a href="<?= \yii\helpers\Url::to(['view', 'id' => $model->id]) ?>" data-href="<?= \yii\helpers\Url::to(['hit', 'id' => $model->id]) ?>" class="green-button js-addHit">Chi tiết <i class="glyphicon glyphicon-chevron-right"></i></a>
        </div>
    </div>
</section>
