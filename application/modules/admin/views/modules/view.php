<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Modules */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Modules Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modules-modules-view">

    <p>
        <?php echo Html::a(Yii::t('admin', 'Update'), ['update', 'id' => $model->module_id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('admin', 'Delete'), ['delete', 'id' => $model->module_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('admin', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'module_id',
            'name',
            'class',
            'isFrontend',
            'controllerNamespace',
            'viewPath',
            'isAdmin',
            'BackendControllerNamespace',
            'AdminViewPath',
            'title',
            'icon',
            'settings:ntext',
            'order',
            'status',
        ],
    ]) ?>

</div>
