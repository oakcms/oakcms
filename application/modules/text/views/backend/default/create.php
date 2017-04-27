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

$this->title = Yii::t('text', 'Create custom block');
$this->params['breadcrumbs'][] = ['label' => Yii::t('text', 'Texts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="text-create">

    <?= $this->render('_form', [
        'model' => $model,
        'lang'  => $lang,
        'layouts' => $layouts,
        'positions' => $positions,
        'menus' => $menus
    ]) ?>

</div>
