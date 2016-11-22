<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.07.2016
 * Project: events.timhome.vn.loc
 * File name: view.php
 *
 * @var $model \app\modules\catalog\models\CatalogItems;
 * @var $this \yii\web\View;
 */

use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->setSeoData($model->meta_title, $model->meta_keywords, $model->meta_description);

$formModel = new \app\modules\system\models\SystemBackCall();
$formModel->id_event = $model->id;

if(isset($model->emails) AND $model->emails != '') {
    $formModel->emails = $model->emails;
} else {
    $formModel->emails = \Yii::$app->getModule('admin')->activeModules['system']->settings['BackCallEmail']['value'];
}

$formModel->title = $model->title;

$assets = \app\templates\frontend\base\assets\BaseAsset::register($this);

$this->registerJsFile('//maps.googleapis.com/maps/api/js?key=AIzaSyB5wwggzEmiOaEvhIZXTQIPc31fhMk_YkY', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile($assets->baseUrl.'/js/map.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile($assets->baseUrl.'/js/FlipClock/flipclock.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile($assets->baseUrl.'/js/FlipClock/flipclock.vn.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile($assets->baseUrl.'/js/owl.carousel/owl.carousel.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile($assets->baseUrl.'/js/owl.carousel/assets/owl.carousel.css');

$timer = $model->date_event - time();
$this->registerJs('$("#gallery").owlCarousel({items: 1, lazyLoad: true, loop: true});var clock = $(\'#getting-started\').FlipClock('.$timer.', {clockFace: \'DailyCounter\',countdown: true,defaultLanguage: \'vn\'});', 3);

$this->bodyClass[] = 'second-page';
$this->bodyClass[] = 'catalog-'.$model->id;
?>
<!--
<div class="container button-return">
    <a href="<?/*= \yii\helpers\Url::to(['index']) */?>" class="button-blue">
        <i class="glyphicon glyphicon-chevron-left"></i> <?/*= Yii::t('catalog', 'Trở lại danh sách') */?>
    </a>
</div>-->

<section class="container invite-block">
    <div class="header-invite">
        <div class="col-sm-8 logo-gk-address">
            <div class="logo-invite">
                <?if(isset($model->logo_company) AND $model->logo_company != ''): ?>
                    <img src="<?= $model->logo_company ?>" alt="">
                <?endif?>
            </div>
            <div class="ttl-address-invite">
                <div class="ttl-invite"><?= $model->title ?></div>
                <div class="address-invite"><?= $model->address ?></div>
            </div>
        </div>
        <div class="col-sm-4 logo-phone-comp">
            <div class="logo-comp">
                <?if(isset($model->logo_invest) AND $model->logo_invest != ''): ?>
                <img src="<?= $model->logo_invest ?>" alt="">
                <?endif?>
            </div>
            <div class="phone-comp">
                <div class="desc-phone-comp">Liên lạc chủ đầu tư</div>
                <div class="nmb-phn-comp"><?= $model->invest_phone ?></div>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="content-invite">
        <div class="header-cntnt">
            <div class="col-md-4 nm-dt">
                <div class="ivent-name">
                    <?= $model->name_announcement ?>
                </div>
                <div class="ivent-date"><?= Yii::$app->formatter->asDate($model->date_event, 'dd.MM.Y') ?></div>
            </div>
            <div class="col-md-8 ivent-counter">
                <div class="row">
                    <div id="getting-started"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="ttl-dsc">
            <div class="dsc">
                <?= $model->description ?>
            </div>
            <div class="gk-cntr-bttn-add">
                <div class="gk-cntr">
                    <div class="ttl-gk-cntr">Đã quan tâm:</div>
                    <div class="gk-cntr-nmb">
                        <?= $model->hit ?> <span>người</span>
                    </div>
                </div>
                <button class="green-button" data-toggle="modal" data-target="#ModalFeedBack"><?= $model->work_with_us ? 'Đăng ký nhận sự kiện mới' : 'Đăng ký tham gia sự kiện' ?></button>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="gallery-invite">
        <?if($model->medias):?>
        <div id="gallery">
            <?foreach ($model->medias as $k=>$item):?>
            <div class="item item-<?= $k ?>">
                <img src="<?= $item->bigImage ?>" alt="<?= $item->file_title ?>" class="img-responsive block-center">
            </div>
            <?endforeach;?>
        </div>
        <?endif;?>
        <div class="dscr-gall">
            <div class="col-md-6 first-col">
                <div class="col-sm-6 ff-col">
                    <div class="ttl"><?= $model->field_1_title ?></div>
                    <div class="desc"><?= $model->field_1_value ?></div>
                </div>
                <div class="col-sm-6 fs-col">
                    <div class="ttl">
                        <?= $model->field_2_title ?>
                    </div>
                    <div class="desc"><?= $model->field_2_value ?></div>
                </div>
            </div>
            <div class="col-md-6 second-col">
                <div class="col-sm-6 sf-col">
                    <div class="ttl"><?= $model->field_3_title ?></div>
                    <div class="desc">
                        <?= $model->field_3_value ?>
                    </div>
                </div>
                <div class="col-sm-6 ss-col">
                    <div class="ttl"><?= $model->field_4_title ?></div>
                    <div class="desc"><?= $model->field_4_value ?></div>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>
</section>

<section class="container invite-map">
    <div class="ttl-map">Vị trí và địa chỉ</div>
    <div class="address-map"><?= $model->address ?></div>
    <div class="map-img">
        <?php $LatLng = explode(',', $model->map_coordinates); ?>
        <div id="map-container">
            <div class="btn-full-screen" style="text-align: right">
                <button id="btn-enter-full-screen">Enter full Screen</button>
                <button id="btn-exit-full-screen" style="display: none">Exit full Screen</button>
            </div>
            <div id="map-canvas" data-lat="<?= $LatLng[0] ?>" data-lng="<?= $LatLng[1] ?>" data-assets="<?= $assets->baseUrl ?>"></div>
        </div>
    </div>
</section>

<section class="container descroption-invite">
    <div id="layer-reade">
        <div class="dsc-description">
            <?= $model->body ?>
        </div>

        <div class="layer-reade-more">
            <div class="reade-more js-reade-more"></div>
        </div>
    </div>
</section>

<!--<section class="container invite-nav">
    <div class="col-sm-4 frst-btn">
        <?/*if($model->previous):*/?>
        <a href="<?/*= \yii\helpers\Url::to(['/system/default/view', 'id' => $model->previous->id]) */?>" class="button-blue">
            <i class="glyphicon glyphicon-chevron-left"></i> Căn hộ trước
        </a>
        <?php /*else: */?>
            <a href="<?/*= Url::to(['/system/default/view', 'id' => $model->last->id]) */?>" class="button-blue">
                Căn hộ sau <i class="glyphicon glyphicon-chevron-right"></i>
            </a>
        <?/*endif;*/?>
    </div>
    <div class="col-sm-4 scnd-btn">
        <a href="<?/*= $model->link_timhome */?>" class="orange-button">
            Gửi
        </a>
    </div>
    <div class="col-sm-4 trd-btn">
        <?php /*if($model->next):*/?>
        <a href="<?/*= Url::to(['/system/default/view', 'id' => $model->next->id]) */?>" class="button-blue">
            Căn hộ sau <i class="glyphicon glyphicon-chevron-right"></i>
        </a>
        <?php /*else: */?>
        <a href="<?/*= Url::to(['/system/default/view', 'id' => $model->first->id]) */?>" class="button-blue">
            Căn hộ sau <i class="glyphicon glyphicon-chevron-right"></i>
        </a>
        <?/*endif;*/?>
    </div>
</section>-->

<section class="container-fluid invite-form">
    <div class="container form-block">
        <?php
        $form = ActiveForm::begin([
            'method' =>'POST',
            'id' => 'CallForm',
            'action' => Url::to(['/back-call'])
        ]);
        ?>
            <div class="ttl"><?= $model->work_with_us ? 'Đăng ký nhận sự kiện mới' : 'Đăng ký tham gia sự kiện' ?></div>
            <div class="form-group">
                <?php echo $form->field($formModel, 'name')->textInput(['placeholder'=>Yii::t('system', 'Tên không được để trống')])->label(false) ?>
            </div>
            <div class="form-group">
                <?php echo $form->field($formModel, 'email')->textInput(['placeholder'=>Yii::t('system', 'Email của bạn')])->label(false) ?>
            </div>
            <?if(!$model->work_with_us):?>
            <div class="form-group">
                <?php echo $form->field($formModel, 'phone')->textInput(['placeholder'=>Yii::t('system', 'Số điện thoại không được để trống')])->label(false) ?>
            </div>
            <?endif;?>
            <?= $form->field($formModel, 'emails')->hiddenInput()->label(false) ?>
            <?= $form->field($formModel, 'title')->hiddenInput()->label(false) ?>
            <?= $form->field($formModel, 'id_event')->hiddenInput()->label(false) ?>
            <div class="cntr-btn-block">
                <div class="gk-cntr">
                    <div class="ttl-gk-cntr">
                        Đã quan tâm:
                    </div>
                    <div class="gk-cntr-nmb">
                        <?= $model->hit ?> <span>người</span>
                    </div>
                </div>
                <button type="submit" class="green-button">Gửi</button>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>

<div id="ModalSuccess" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="ModalSuccessLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="ModalSuccessLabel">Thành công</h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div id="ModalFeedBack" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalFeedBackLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="ModalFeedBackLabel"><?= Yii::t('app', 'Đăng kí'); // ĐĂNG KÝ ĐẶT MUA ?></h4>
            </div>
            <div class="modal-body">
                <?php
                $form = ActiveForm::begin([
                    'method' =>'POST',
                    'id' => 'CallFormModal',
                    'action' => Url::to(['/back-call'])
                ]);
                ?>
                <div class="form-group">
                    <?php echo $form->field($formModel, 'name')->textInput(['placeholder'=>Yii::t('system', 'Tên không được để trống')])->label(false) ?>
                </div>
                <div class="form-group">
                    <?php echo $form->field($formModel, 'email')->textInput(['placeholder'=>Yii::t('system', 'Email của bạn')])->label(false) ?>
                </div>
                <?if(!$model->work_with_us):?>
                <div class="form-group">
                    <?php echo $form->field($formModel, 'phone')->textInput(['placeholder'=>Yii::t('system', 'Số điện thoại không được để trống')])->label(false) ?>
                </div>
                <?endif;?>
                <?= $form->field($formModel, 'emails')->hiddenInput()->label(false) ?>
                <?= $form->field($formModel, 'title')->hiddenInput()->label(false) ?>
                <?= $form->field($formModel, 'id_event')->hiddenInput()->label(false) ?>

                <div class="cntr-btn-block">
                    <button type="submit" class="btn btn-block orange-button">Gửi</button>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
