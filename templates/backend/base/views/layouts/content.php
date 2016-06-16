<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 03.07.2015
 * Project: oakcms2
 * File name: content.php
 *
 * @var $this \app\components\AdminView;
 */

use yii\widgets\Breadcrumbs;
use app\modules\admin\widgets\Button;

$this->bodyClass = 'skin-blue sidebar-mini';
?>
<?php $this->beginContent('@app/templates/backend/base/views/layouts/_clear.php'); ?>
<?= \Yii::$app->view->renderFile('@app/templates/backend/base/views/layouts/blocks/header.php'); ?>
<?= \Yii::$app->view->renderFile('@app/templates/backend/base/views/layouts/blocks/sidebar.php'); ?>
<div class="content-wrapper">

    <? /*
    <section class="content-header">
        <h1>
            Sidebar Collapsed
            <small>Layout with collapsed sidebar on load</small>
        </h1>
        <?= Breadcrumbs::widget([
            'encodeLabels' => false,
            'homeLink' => ['label' => '<i class="fa fa-home" aria-hidden="true"></i>'.Yii::t('admin', 'Home'), 'url' => ['/admin']],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    */ ?>
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?php if (isset($this->params['title_icon'])): ?>
                        <i class="<?= $this->params['title_icon'] ?>"></i>
                    <?php endif; ?>
                    <span class="caption-subject bold uppercase"><?php echo $this->title ?></span>
                    <?php if (isset($this->params['subtitle'])): ?>
                        <span class="caption-helper"><?php echo $this->params['subtitle'] ?></span>
                    <?php endif; ?>
                </h3>

                <?php if(isset($this->params['actions_buttons'])): ?>
                    <div class="actions pull-right">
                        <?php
                        if(in_array(Yii::$app->controller->action->id, ['update', 'create', 'setting'])) {
                            echo Button::widget(
                                    [
                                        'label' => Yii::t('admin', 'Back'),
                                        'options' => ['onclick' => 'window.history.back()'],
                                        'icon' => 'fa fa-angle-left',
                                        'iconPosition' => Button::ICON_POSITION_LEFT,
                                        'size' => Button::SIZE_SMALL,
                                        'disabled' => false,
                                        'block' => false,
                                        'type' => Button::TYPE_CIRCLE,
                                    ]
                                ).' ';
                        }
                        foreach($this->params['actions_buttons'] as $item) {
                            if(isset($item['dropdown'])) {
                                echo \app\modules\admin\widgets\ButtonDropdown::widget($item).' ';
                            } else {
                                echo Button::widget($item).' ';
                            }

                        }
                        ?>
                    </div>
                <?php endif ?>
            </div>
            <div class="box-body">
                <?= $content ?>
            </div>
            <div class="box-footer">
                <?php if(isset($this->params['actions_buttons'])): ?>
                    <div class="actions pull-right">
                        <?php
                        if(in_array(Yii::$app->controller->action->id, ['update', 'create', 'setting'])) {
                            echo Button::widget(
                                    [
                                        'label' => Yii::t('backend', 'Back'),
                                        'options' => ['onclick' => 'window.history.back()'],
                                        'icon' => 'fa fa-angle-left',
                                        'iconPosition' => Button::ICON_POSITION_LEFT,
                                        'size' => Button::SIZE_SMALL,
                                        'disabled' => false,
                                        'block' => false,
                                        'type' => Button::TYPE_CIRCLE,
                                    ]
                                ).' ';
                        }
                        foreach($this->params['actions_buttons'] as $item) {
                            if(isset($item['dropdown'])) {
                                echo \yii\bootstrap\ButtonDropdown::widget($item).' ';
                            } else {
                                echo Button::widget($item).' ';
                            }

                        }
                        ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </section>



</div>
<?php $this->endContent() ?>
