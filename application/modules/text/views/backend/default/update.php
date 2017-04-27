<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * @var $this      yii\web\View
 * @var $model     app\modules\text\models\Text
 * @var $lang      \app\modules\language\models\Language
 * @var $layouts   array
 * @var $positions array
 * @var $menus     \app\modules\menu\models\MenuItem
 */

$this->title = Yii::t('admin', 'Update {modelClass}: ', [
        'modelClass' => Yii::t('text', 'Custom Block'),
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('text', 'Custom Blocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update') . ': ' . $model->title;
?>
<div class="text-update">

    <?= $this->render('_form', [
        'model' => $model,
        'lang'  => $lang,
        'layouts' => $layouts,
        'positions' => $positions,
        'menus' => $menus
    ]) ?>

</div>
