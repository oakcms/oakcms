<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

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
<div class="<?= $model->getSetting('cssClass') ?>" id="<?= $model->getSetting('id') ?>">
    <?if((int)$model->getSetting('hideTitle') !== 1):?>
        <h2><?= $model->title ?></h2>
        <?if($model->subtitle != ''):?>
        <h3><?= $model->subtitle ?></h3>
        <?endif;?>
    <?endif;?>
    <?= $model->text ?>
    [widgetkit id="<?= $model->getSetting('widgetkit') ?>"]
</div>
