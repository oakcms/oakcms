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

<div class="block_text_consalt">
    <div class="container">
        <div class="item inline-layout">
            <div class="name"><?= $model->getSetting('name1') ?></div>
            <div class="text_desc"><?= $model->getSetting('description1') ?></div>
        </div>
        <div class="item inline-layout">
            <div class="name"><?= $model->getSetting('name2') ?></div>
            <div class="text_desc"><?= $model->getSetting('description2') ?></div>
        </div><a href="#<?= $model->getSetting('form') ?>" class="button open_modal"><?= $model->getSetting('buttonName') ?></a>
    </div>
</div>
