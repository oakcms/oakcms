<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

use yii\helpers\Html;

/**
 * @var $category
 * @var $widget
 */
?>
<div class="dd-handle dd3-handle"></div>
<div class="dd3-content">
    <?= Html::a($category['name'], [$widget->updateUrl, 'id' => $category[$widget->idField]]) ?>
    <div class="pull-right">
        <?php if ($widget->viewUrl) { ?>
            <?php if ($widget->viewUrlToSearch) { ?>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-eye-open">',
                    [$widget->viewUrl, $widget->viewUrlModelName => [$widget->viewUrlModelField => $category[$widget->idField]]],
                    ['title' => 'Смотреть', 'style' => 'margin-right:0']
                ); ?>
            <?php } else { ?>
                <?= Html::a('<span class="glyphicon glyphicon-eye-open">', [$widget->viewUrl, 'id' => $category[$widget->idField]], ['class' => 'btn btn-xs green', 'title' => 'Смотреть']); ?>
            <?php } ?>
        <?php } ?>

        <?= Html::a('<i class="fa fa-trash"></i>', [$widget->deleteUrl, 'id' => $category[$widget->idField]], [
            'title'        => \Yii::t('backend', 'Delete'),
            'aria-label'   => \Yii::t('yii', 'Delete'),
            'data-confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method'  => 'post',
        ]) ?>
    </div>
</div>
