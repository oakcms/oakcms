<?php

use yii\bootstrap\Html;
use app\modules\admin\widgets\ActiveForm;
use app\modules\admin\widgets\Button;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model \app\modules\system\models\SystemSettings
 *
 */

$this->title = Yii::t('system', 'System Settings');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'label' => Yii::t('admin', 'Update'),
        'options' => [
            'form' => 'settings',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'green-jungle'
    ],
]
?>
<div class="system-settings-index">
    <?php ActiveForm::begin([
        'id' => 'settings'
    ]) ?>

    <?foreach ($model as $item):?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="<?= $item->param_name ?>">
                <?= Yii::t('system', $item->param_name) ?>
            </label>
            <div class="col-md-9">
                <?= $item->renderField(); ?>
            </div>
        </div>
    <?endforeach;?>
    <?php ActiveForm::end() ?>
</div>
