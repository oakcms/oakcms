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
?>


<div class="block_button_consalt block_button_consalt_border <?= $model->getSetting('cssClass') ?>">
    <a href="<?= Url::to([$model->getSetting('link')]) ?>" class="button">
        <?= $model->getSetting('buttonName') ?>
    </a>
</div>
