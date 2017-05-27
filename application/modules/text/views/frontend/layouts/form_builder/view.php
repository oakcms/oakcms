<?php
/**
 * @package    oakcms/oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * @var $this \app\components\CoreView
 * @var $model \app\modules\text\models\Text
 */

$form = $model->getSetting('form');

$model = \app\modules\form_builder\models\FormBuilderForms::find()->where(['id' => $form])->one();

if($model === null) {
    return null;
}

echo \app\modules\form_builder\widgets\ShortCode::render($model);
