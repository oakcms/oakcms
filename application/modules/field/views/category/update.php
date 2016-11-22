<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\shop\models\Category */

$this->title = 'Обновить категорию: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Обновить';

?>
<div class="category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
