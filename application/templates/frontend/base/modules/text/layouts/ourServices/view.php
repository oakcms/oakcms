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
<section class="<?= $model->getSetting('cssClass') ?>">
    <article class="container">
        <h2><?= $model->getSetting('title') ?></h2>
        <p><?= $model->getSetting('description') ?></p>
    </article>
    <div class="block_advantages">
        <div class="container">
            <div class="list_advantages inline-layout col-2">
                <div class="item inline-layout">
                    <div class="block_text">
                        <h4><?= $model->getSetting('firstBlockTitle') ?></h4>
                        <p><?= $model->getSetting('firstBlockDescription') ?></p>
                    </div>
                    <div class="block_img">
                        <img src="<?= $model->getSetting('firstBlockImage') ?>" alt="<?= $model->getSetting('firstBlockImageAlt') ?>">
                    </div>
                </div>
                <div class="item inline-layout">
                    <div class="block_text">
                        <h4><?= $model->getSetting('secondBlockTitle') ?></h4>
                        <p><?= $model->getSetting('secondBlockDescription') ?></p>
                    </div>
                    <div class="block_img">
                        <img src="<?= $model->getSetting('secondBlockImage') ?>" alt="<?= $model->getSetting('secondBlockImageAlt') ?>">
                    </div>
                </div>
                <div class="item inline-layout">
                    <div class="block_text">
                        <h4><?= $model->getSetting('thirdBlockTitle') ?></h4>
                        <p><?= $model->getSetting('thirdBlockDescription') ?></p>
                    </div>
                    <div class="block_img">
                        <img src="<?= $model->getSetting('thirdBlockImage') ?>" alt="<?= $model->getSetting('thirdBlockImageAlt') ?>">
                    </div>
                </div>
                <div class="item inline-layout">
                    <div class="block_text">
                        <h4><?= $model->getSetting('fourthBlockTitle') ?></h4>
                        <p><?= $model->getSetting('fourthBlockDescription') ?></p>
                    </div>
                    <div class="block_img">
                        <img src="<?= $model->getSetting('fourthBlockImage') ?>" alt="<?= $model->getSetting('fourthBlockImageAlt') ?>">
                    </div>
                </div>
                <div class="item inline-layout">
                    <div class="block_text">
                        <h4><?= $model->getSetting('fifthBlockTitle') ?></h4>
                        <p><?= $model->getSetting('fifthBlockDescription') ?></p>
                    </div>
                    <div class="block_img">
                        <img src="<?= $model->getSetting('fifthBlockImage') ?>" alt="<?= $model->getSetting('fifthBlockImageAlt') ?>">
                    </div>
                </div>
                <div class="item inline-layout">
                    <div class="block_text">
                        <h4><?= $model->getSetting('sixthBlockTitle') ?></h4>
                        <p><?= $model->getSetting('sixthBlockDescription') ?></p>
                    </div>
                    <div class="block_img">
                        <img src="<?= $model->getSetting('sixthBlockImage') ?>" alt="<?= $model->getSetting('sixthBlockImageAlt') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>