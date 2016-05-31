<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 23.05.2016
 * Project: oakcms
 * File name: settings.php
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\admin\widgets\Button;

$this->title = $model->title;

$this->params['actions_buttons'] = [
    [
        'label' => Yii::t('admin', 'Restore default settings'),
        'options' => [
            'type' => false,
            'href' => Url::to(['/admin/modules/restore-settings', 'id' => $model->module_id])
        ],
        'tagName' => 'a',
        'icon' => 'glyphicon glyphicon-flash',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'yellow-soft'
    ],
    [
        'label' => Yii::t('admin', 'Update'),
        'options' => [
            'form' => 'modules-modules-id',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ]
];

?>

<?php if(sizeof($model->settings) > 0) : ?>
    <?= Html::beginForm('', 'post', ['id' => 'modules-modules-id']); ?>
    <?php foreach($model->settings as $key => $value) : ?>
        <?php if(!is_bool($value)) : ?>
            <div class="form-group">
                <label><?= Yii::t('admin', $key); ?></label>
                <?= Html::input('text', 'Settings['.$key.']', $value, ['class' => 'form-control']); ?>
            </div>
        <?php else : ?>
            <div class="checkbox">
                <label>
                    <?= Html::checkbox('Settings['.$key.']', $value, ['uncheck' => 0])?> <?= $key ?>
                </label>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php Html::endForm(); ?>
<?php else : ?>
    <?= $model->title ?> <?= Yii::t('admin', 'module doesn`t have any settings.') ?>
<?php endif; ?>
