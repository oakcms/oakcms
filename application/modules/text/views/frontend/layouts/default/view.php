<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

/**
 * @var $model \app\modules\text\models\Text
 */

use yii\helpers\Html;
?>
<div id="<?= $model->getSetting('id') ?>">
    <div class="container <?= $model->getSetting('cssClass') ?>">
        <?php if((int)$model->getSetting('hideTitle') !== 1): ?>
            <?= Html::tag($model->getSetting('headingSize'), $model->title) ?>
            <?php if ($model->subtitle != ''): ?>
                <h3><?= $model->subtitle ?></h3>
            <?php endif; ?>
        <?php endif; ?>
        <?= $model->text ?>
    </div>
</div>
