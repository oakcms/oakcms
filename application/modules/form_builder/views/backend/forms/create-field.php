<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

/**
 * @var $fields array
 * @var $form_id integer
 */
?>

<div class="modal-header">
    <h3>
        <?= Yii::t('form_builder', 'Select type') ?>
    </h3>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <?php foreach ($fields as $type => $field): ?>
                <div class="col-xs-4 mb-10">
                    <a href="<?= \yii\helpers\Url::to(['', 'id' => $form_id, 'type' => $type]) ?>" class="btn btn-default btn-block">
                        <?= $field['icon'] ?> <?= $field['title'] ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
