<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\modules\admin\widgets\Button;

$asset = \app\templates\backend\base\assets\BaseAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\MenuItem */
/* @var $sourceModel app\modules\menu\models\MenuItem */
/* @var $linkParamsModel app\modules\menu\models\MenuLinkParams */
/* @var $form yii\bootstrap\ActiveForm */

$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'),
        'options' => [
            'form' => 'menu-item-id',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ],
    [
        'label' => Yii::t('admin', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#menu-item-id")',
        ],
        'icon' => 'fa fa-check-circle',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ],
    [
        'label' => Yii::t('admin', 'Save & Create new'),
        'options' => [
            'onclick' => 'sendFormCreate("#menu-item-id")',
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ]
];

\app\widgets\ParseUrlAsset::register($this);
?>

    <div class="menu-form">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'menu-item-id'
        ]); ?>

        <?= $form->errorSummary([$model, $linkParamsModel]) ?>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'title', ['wrapperOptions' => ['class' => 'col-sm-9']])->textInput(['maxlength' => 1024, 'placeholder' => isset($sourceModel) ? $sourceModel->title : null]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'alias', ['wrapperOptions' => ['class' => 'col-sm-9']])->textInput(['maxlength' => 255, 'placeholder' => Yii::t('menu', 'Auto-generate')]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'language', ['horizontalCssClasses' => ['wrapper' => 'col-xs-8 col-sm-9', 'label' => 'col-xs-4 col-sm-3']])->dropDownList(\app\modules\language\models\Language::getAllLangR(), ['prompt' => Yii::t('menu', 'Select ...')]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'note', ['wrapperOptions' => ['class' => 'col-sm-9']])->textInput(['maxlength' => 255]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'menu_type_id', ['wrapperOptions' => ['class' => 'col-sm-9']])->dropDownList(['' => Yii::t('menu', 'Select ...')] + \yii\helpers\ArrayHelper::map(\app\modules\menu\models\MenuType::find()->all(), 'id', 'title')) ?>
            </div>
            <div class="col-sm-6">
                <?php
                $idLanguage = Html::getInputId($model, 'language');
                $idMenu_type_id = Html::getInputId($model, 'menu_type_id');
                $idParent_id = Html::getInputId($model, 'parent_id');
                echo $form->field($model, 'parent_id', [
                    'wrapperOptions' => ['class' => 'col-sm-9'],
                    'inputTemplate'  => '<div class="input-group select2-bootstrap-append">{input}' . \app\widgets\ModalIFrame::widget([
                            'options'       => [
                                'class' => 'input-group-addon',
                                'title' => \Yii::t('menu', 'Select Category'),
                            ],
                            'label'         => '<i class="glyphicon glyphicon-folder-open"></i>',
                            'url'           => ['select', 'modal' => true, 'MenuItemSearch[excludeItem]' => $model->isNewRecord ? null : $model->id],
                            'dataHandler'   =>
                                <<<JS
                                function(data) {
    $("#{$idParent_id}").html('<option value="' + data.id + '">' + data.title + '</option>').val(data.id).trigger('change');
}
JS
                            ,
                            'actionHandler' => 'function(url) {return (new URI(url)).addSearch("MenuItemSearch[language]", $("#' . $idLanguage . '").val()).addSearch("MenuItemSearch[menu_type_id]", $("#' . $idMenu_type_id . '").val())}',
                        ]) . '</div>',
                ])->widget(\kartik\select2\Select2::className(), [
                    'initValueText' => $model->parent ? ($model->parent->isRoot() ? Yii::t('menu', 'Top Level') : $model->parent->title) : null,
                    'theme'         => \kartik\select2\Select2::THEME_BOOTSTRAP,
                    'pluginOptions' => [
                        'allowClear'  => true,
                        'placeholder' => Yii::t('menu', 'Top Level'),
                        'ajax'        => [
                            'url'  => \yii\helpers\Url::to(['item-list', 'exclude' => $model->isNewRecord ? null : $model->id]),
                            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term, language:$("#' . $idLanguage . '").val(), menu_type_id:$("#' . $idMenu_type_id . '").val()}; }'),
                        ],
                    ],
                ]) ?>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'status', ['wrapperOptions' => ['class' => 'col-sm-9']])->dropDownList(['' => Yii::t('menu', 'Select ...')] + $model->statusLabels()) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'ordering', ['horizontalCssClasses' => ['wrapper' => 'col-xs-8 col-sm-4', 'label' => 'col-xs-4 col-sm-3']])->textInput() ?>
            </div>
        </div>

        <?php //= $form->field($model, 'path')->textInput(['maxlength' => 2048]) ?>

        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#main-data" data-toggle="tab"><?= Yii::t('menu', 'Main') ?></a>
            </li>
            <li>
                <a href="#link-options" data-toggle="tab"><?= Yii::t('menu', 'Link params') ?></a>
            </li>
            <li>
                <a href="#meta-options" data-toggle="tab"><?= Yii::t('menu', 'SEO') ?></a>
            </li>
        </ul>
        <br />
        <div class="tab-content">
            <div id="main-data" class="tab-pane active">
                <?= $form->field($model, 'link_type')->dropDownList(\app\modules\menu\models\MenuItem::linkTypeLabels()) ?>

                <?php $this->registerJs("$('#" . Html::getInputId($model, 'link_type') . "').change(function (event){
                if($(this).val() === '" . \app\modules\menu\models\MenuItem::LINK_ROUTE . "') {
                    $('a#router').attr('href', " . \yii\helpers\Json::encode(\yii\helpers\Url::toRoute(['routers'])) . ");
                    $('#".Html::getInputId($model, 'link')."').addClass('disabled');
                } else {
                    $('a#router').attr('href', " . \yii\helpers\Json::encode(\yii\helpers\Url::toRoute(['select', 'MenuItemSearch[link_type]' => \app\modules\menu\models\MenuItem::LINK_ROUTE])) . ")
                    $('#".Html::getInputId($model, 'link')."').removeClass('disabled');
                }
            }).change()") ?>

                <?php
                $linkLabel = Html::activeLabel($model, 'link');
                $linkInputId = Html::getInputId($model, 'link');

                echo $form->field($model, 'link', [
                    'template' => "{label}\n{beginWrapper}\n<div class=\"input-group\">{input}{controls}</div>\n{error}\n{endWrapper}\n{hint}",
                    'parts'    => [
                        '{controls}' => \app\widgets\ModalIFrame::widget([
                            'options'     => [
                                'id'    => 'router',
                                'class' => 'input-group-btn',
                            ],
                            'url'         => ['routers'],
                            'label'       => Html::tag('span', '<span class="glyphicon glyphicon-folder-open"></span>', ['class' => 'btn btn-default']),
                            'dataHandler' => "function(data){
                                    $('#{$linkInputId}').val(data.route)
                                }",
                        ]),
                    ],
                ])->textInput(['maxlength' => 1024]) ?>

                <?= $form->field($model, 'access_rule')->dropDownList(\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'), [
                    'prompt' => 'Не выбрано',
                ]) ?>

                <?= $form->field($model, 'secure')->dropDownList([
                    ''  => 'No',
                    '1' => 'Yes',
                ]) ?>

                <?php
                // формируем список шаблонов пункта меню
                $layouts = Yii::$app->controller->module->getMenuItemLayouts();

                if ($model->layout_path && !array_key_exists($model->layout_path, $layouts)) {
                    // если шаблона пункта меню нету в списке(кастомный шаблон), добавляем его в список
                    $layouts[$model->layout_path] = $model->layout_path;
                }

                echo $form->field($model, 'layout_path')->widget(\kartik\select2\Select2::className(), [
                    'data'          => $layouts,
                    'theme'         => \kartik\select2\Select2::THEME_BOOTSTRAP,
                    'pluginOptions' => [
                        'tags'        => true,
                        'allowClear'  => true,
                        'multiple'    => false,
                        'placeholder' => Yii::t('menu', 'Default'),
                    ],
                ]); ?>
            </div>

            <div id="link-options" class="tab-pane">
                <?= $form->field($linkParamsModel, 'title')->textInput() ?>

                <?= $form->field($linkParamsModel, 'class')->textInput() ?>

                <?= $form->field($linkParamsModel, 'style')->textInput() ?>

                <?= $form->field($linkParamsModel, 'target')->textInput() ?>

                <?= $form->field($linkParamsModel, 'onclick')->textInput() ?>

                <?= $form->field($linkParamsModel, 'rel')->textInput() ?>
            </div>

            <div id="meta-options" class="tab-pane">
                <?= $form->field($model, 'metatitle')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($model, 'metakey')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($model, 'metadesc')->textarea(['maxlength' => 2048]) ?>

                <?= $form->field($model, 'robots')->dropDownList([
                    'index, follow'     => 'Index, Follow',
                    'noindex, follow'   => 'No index, follow',
                    'index, nofollow'   => 'Index, No follow',
                    'noindex, nofollow' => 'No index, no follow',
                ], [
                    'prompt' => 'Не выбрано',
                ]) ?>
            </div>
        </div>

        <?= Html::activeHiddenInput($model, 'lock') ?>

        <?php ActiveForm::end(); ?>

    </div>
<?php $this->registerJs('$("#' . Html::getInputId($model, 'menu_type_id') . '").change()', \yii\web\View::POS_READY);
?>

<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
    function sendFormCreate(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='createNew'>"));
        $(elm).submit();
        return false;
    }
</script>
