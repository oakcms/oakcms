<?php
/**
 * @package    oakcms/oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * @var $model \app\modules\text\models\Text
 */

use yii\helpers\Html;
?>

<?php if((int)$model->getSetting('hide_title') !== 1): ?>
    <?= Html::tag($model->getSetting('title_size', 'h2'), $model->title) ?>
    <?php if ($model->subtitle != ''): ?>
        <?= Html::tag($model->getSetting('sub_title_size', 'h2'), $model->subtitle) ?>
    <?php endif; ?>
<?php endif; ?>

<?= $model->text ?>
