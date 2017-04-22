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

?>

<div id="block_vacancy" class="block_vacancy <?= $model->getSetting('cssClass') ?>">
    <div class="container inline-layout col-5">
        <div class="item inline-layout">
            <a href="<?= $model->getSetting('link_1') ?>" class="name"><?= $model->getSetting('title_1') ?></a>
            <div class="img-holder">
                <a href="<?= $model->getSetting('link_1') ?>">
                    <img src="<?= $model->getSetting('image_1') ?>" alt="<?= $model->getSetting('title_1') ?>">
                </a>
            </div>
            <div class="block_text">
                <?= $model->getSetting('description_1') ?>
            </div>
        </div>
        <div class="item inline-layout">
            <a href="<?= $model->getSetting('link_2') ?>" class="name"><?= $model->getSetting('title_2') ?></a>
            <div class="img-holder">
                <a href="<?= $model->getSetting('link_2') ?>">
                    <img src="<?= $model->getSetting('image_2') ?>" alt="<?= $model->getSetting('title_2') ?>">
                </a>
            </div>
            <div class="block_text">
                <?= $model->getSetting('description_2') ?>
            </div>
        </div>
        <div class="item inline-layout">
            <a href="<?= $model->getSetting('link_3') ?>" class="name"><?= $model->getSetting('title_3') ?></a>
            <div class="img-holder">
                <a href="<?= $model->getSetting('link_3') ?>">
                    <img src="<?= $model->getSetting('image_3') ?>" alt="<?= $model->getSetting('title_3') ?>">
                </a>
            </div>
            <div class="block_text">
                <?= $model->getSetting('description_3') ?>
            </div>
        </div>
        <div class="item inline-layout">
            <a href="<?= $model->getSetting('link_4') ?>" class="name"><?= $model->getSetting('title_4') ?></a>
            <div class="img-holder">
                <a href="<?= $model->getSetting('link_4') ?>">
                    <img src="<?= $model->getSetting('image_4') ?>" alt="<?= $model->getSetting('title_4') ?>">
                </a>
            </div>
            <div class="block_text">
                <?= $model->getSetting('description_4') ?>
            </div>
        </div>
        <div class="item inline-layout">
            <a href="<?= $model->getSetting('link_5') ?>" class="name"><?= $model->getSetting('title_5') ?></a>
            <div class="img-holder">
                <a href="<?= $model->getSetting('link_5') ?>">
                    <img src="<?= $model->getSetting('image_5') ?>" alt="<?= $model->getSetting('title_5') ?>">
                </a>
            </div>
            <div class="block_text">
                <?= $model->getSetting('description_5') ?>
            </div>
        </div>
    </div>
</div>
