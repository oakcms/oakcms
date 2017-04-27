<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */


/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\MenuItem */
/* @var $sourceModel app\modules\menu\models\MenuItem */
/* @var $linkParamsModel app\modules\menu\models\MenuLinkParams */

$this->title = Yii::t('menu', 'Add Menu Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-create">

    <?= $this->render('_form', [
        'model'           => $model,
        'sourceModel'     => $sourceModel,
        'linkParamsModel' => $linkParamsModel
    ]) ?>

</div>
