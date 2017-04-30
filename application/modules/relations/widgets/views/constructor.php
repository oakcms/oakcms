<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

?>
<div class="relations-constructor">
    <input type="hidden" name="send_relations" value="yes" />
    <div class="oakcms-relations">

    </div>
    <div class="row oakcms-new-relation">
        <div class="col-md-12 col-lg-12">
            <div class="form-group">
                <?= \app\widgets\ModalIFrame::widget([
                    'label'         => Yii::t('shop', 'Select...') . '<span class="glyphicon glyphicon-plus add-option"></span>',
                    'url'           => ["/admin/relations/default/list", 'model' => $model->getRelatedModel()],
                    'dataHandler'   => '',
                    'actionHandler' => '',
                    'options'       => ['class' => 'btn btn-success'],
                    'popupOptions'  => ['width' => 600]
                ]); ?>
            </div>
        </div>
    </div>
</div>
