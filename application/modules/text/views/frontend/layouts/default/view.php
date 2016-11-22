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
<section class="<?= $model->settings['cssClass']['value'] ?>" id="<?= $model->settings['id']['value'] ?>">
    <?if((int)$model->settings['hideTitle']['value'] !== 1):?>
        <h2><?= $model->title ?></h2>
        <?if($model->subtitle != ''):?>
        <h3><?= $model->subtitle ?></h3>
        <?endif;?>
    <?endif;?>
    <?= $model->text ?>
</section>
