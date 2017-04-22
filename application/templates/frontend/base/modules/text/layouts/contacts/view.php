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


$isHome = (Yii::$app->request->baseUrl.'/index' == Url::to([''])) ? true : false;
?>

<div class="block_contact <?= $model->getSetting('cssClass') ?>">
    <div class="list_contacts">
        <div class="item">
            <h4><?= $model->getSetting('country1') ?></h4>
            <ul class="list_contact_item inline-layout col-3">
                <li class="phone">
                    <h5><?= $model->getSetting('callCenterTitle1') ?></h5>
                    <div class="text_desc"><?= $model->getSetting('callCenterDescription1') ?></div>
                    <a href="<?= $model->getSetting('callCenterPhone1_1') ?>"><?= $model->getSetting('callCenterPhone1_1') ?></a>
                    <a href="<?= $model->getSetting('callCenterPhone1_2') ?>"><?= $model->getSetting('callCenterPhone1_2') ?></a>
                    <a href="<?= $model->getSetting('callCenterPhone1_3') ?>"><?= $model->getSetting('callCenterPhone1_3') ?></a>
                </li>
                <li class="email">
                    <h5><?= $model->getSetting('emailTitle1') ?></h5>
                    <div class="text_desc"><?= $model->getSetting('emailDescription1') ?></div>
                    <a href="mailto:<?= $model->getSetting('email1') ?>"><?= $model->getSetting('email1') ?></a>
                </li>
                <li class="address">
                    <h5><?= $model->getSetting('address1') ?></h5>
                    <div class="text_desc"></div>
                    <address><?= $model->getSetting('country1') ?>, <?= $model->getSetting('city1') ?>, <br><?= $model->getSetting('street1') ?>, <?= $model->getSetting('numberOfHouse1') ?>, <?= $model->getSetting('numberOfOffice1') ?></address>
                    <div class="timework"><?= $model->getSetting('daysAtWork1') ?> - <?= $model->getSetting('hoursAtWork1') ?>, <br><?= $model->getSetting('holidays1') ?>Сб - Вc - выходной</div>
                </li>
            </ul>
            <div class="map_wrap">
                <?= $model->getSetting('map1') ?>
            </div>
        </div>
        <div class="item">
            <h4><?= $model->getSetting('country2') ?></h4>
            <ul class="list_contact_item inline-layout col-3">
                <li class="phone">
                    <h5><?= $model->getSetting('callCenterTitle2') ?></h5>
                    <div class="text_desc"><?= $model->getSetting('callCenterDescription2') ?></div>
                    <a href="<?= $model->getSetting('callCenterPhone2_1') ?>"><?= $model->getSetting('callCenterPhone2_1') ?></a>
                    <a href="<?= $model->getSetting('callCenterPhone2_2') ?>"><?= $model->getSetting('callCenterPhone2_2') ?></a>
                    <a href="<?= $model->getSetting('callCenterPhone2_3') ?>"><?= $model->getSetting('callCenterPhone2_3') ?></a>
                </li>
                <li class="email">
                    <h5><?= $model->getSetting('emailTitle2') ?></h5>
                    <div class="text_desc"><?= $model->getSetting('emailDescription2') ?></div>
                    <a href="mailto:<?= $model->getSetting('email2') ?>"><?= $model->getSetting('email2') ?></a>
                </li>
                <li class="address">
                    <h5><?= $model->getSetting('address2') ?></h5>
                    <div class="text_desc"></div>
                    <address><?= $model->getSetting('country2') ?>, <?= $model->getSetting('city2') ?>, <br><?= $model->getSetting('street2') ?>, <?= $model->getSetting('numberOfHouse2') ?>, <?= $model->getSetting('numberOfOffice2') ?></address>
                    <div class="timework"><?= $model->getSetting('daysAtWork2') ?> - <?= $model->getSetting('hoursAtWork2') ?>, <br><?= $model->getSetting('holidays1') ?>Сб - Вc - выходной</div>
                </li>
            </ul>
            <div class="map_wrap">
                <?= $model->getSetting('map2') ?>
            </div>
        </div>
    </div>
</div>
<div class="form-contact">
    [form_builder id="<?= $model->getSetting('form') ?>"]
</div>
