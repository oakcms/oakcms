<?php
/**
 * @var $model \app\modules\text\models\Text
 * @var $this \app\components\CoreView
 */

use app\modules\catalog\models\forms\Filter;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$formModel = new Filter();
$formModel->load(Yii::$app->request->queryParams);
?>

<div class="form_search">
    <div class="container">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => \yii\helpers\Url::to(['/catalog/filter/index'])
        ]); ?>
            <div class="row">
                <div class="form-holder">
                    <?= Html::activeInput('text', $formModel, 'title', ['placeholder' => Yii::t('catalog', 'Enter the name of the vacancy')]) ?>
                </div>
                <div class="form-holder specialization">
                    <?= Html::activeDropDownList($formModel, 'specialization', $formModel->getSpecializations(), ['prompt' => Yii::t('catalog', 'All specializations')]) ?>
                </div>
                <div class="form-holder country">
                    <?= Html::activeDropDownList($formModel, 'country', $formModel->getCountries(), ['prompt' => Yii::t('catalog', 'Country')]) ?>
                </div>
                <div class="form-holder price">
                    <?= Html::activeInput('text', $formModel, 'price_from', ['placeholder' => Yii::t('catalog', 'Salary from')]) ?>
                </div>
                <div class="form-holder currency">
                    <?= Html::activeDropDownList($formModel, 'currency', $formModel->getCurrencies(), ['prompt' => Yii::t('catalog', 'Currency')]) ?>
                </div>
            </div>
            <div class="row">
                <div class="form-holder checkbox">
                    <?= Html::checkbox('Filter[free]', $formModel->free, ['id' => 'checkbox1']) ?>
                    <label for="checkbox1"><?= $formModel->getAttributeLabel('free') ?></label>
                </div>
                <div class="form-holder checkbox">
                    <?= Html::checkbox('Filter[hot]', $formModel->hot, ['id' => 'checkbox2']) ?>
                    <label for="checkbox2"><?= $formModel->getAttributeLabel('hot') ?></label>
                </div>
                <div class="form-holder checkbox">
                    <?= Html::checkbox('Filter[video]', $formModel->video, ['id' => 'checkbox3']) ?>
                    <label for="checkbox3"><?= $formModel->getAttributeLabel('video') ?></label>
                </div>
                <div class="form-holder">
                    <input type="submit" value="Поиск">
                </div>
            </div>
        <?php $form = ActiveForm::end(); ?>
    </div>
</div>
