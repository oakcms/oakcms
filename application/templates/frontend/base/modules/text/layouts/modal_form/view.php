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
use yii\helpers\Html;
?>

<div id="<?= $model->getSetting('id') ?>" class="modal_div">
    <div class="modal_close"></div>
    <div class="modal-content">
        <div class="form-contact">
            [form_builder id="<?= $model->getSetting('form') ?>"]
        </div>
    </div>
</div>
