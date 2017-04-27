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
/* @var $model app\modules\menu\models\MenuItem */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', $model->menuType->title), 'url' => ['index', 'MenuItemSearch' => ['menu_type_id' => $model->menu_type_id]]];
/*if (($parent = $model->parent) && !$parent->isRoot()) {
    $this->params['breadcrumbs'][] = ['label' => $parent->title, 'url' => ['index', 'MenuItemSearch' => ['parent_id' => $parent->id]]];
}*/
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('menu', 'Add'), ['create', 'menuTypeId' => $model->menu_type_id, 'parentId' => $model->parent_id, 'language' => $model->language], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('menu', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('menu', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('menu', 'Are you sure you want to delete this item?'),
                'method'  => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'translation_id',
            'menu_type_id',
            'parent_id',
            'status',
            [
                'attribute' => 'language',
                'value' => \app\modules\main\widgets\TranslationsBackend::widget(['model' => $model]),
                'format' => 'raw'
            ],
            'title',
            'alias',
            'path',
            'note',
            'link',
            'link_type',
            'link_params:ntext',
            'layout_path',
            'access_rule',
            'metakey',
            'metadesc',
            'robots',
            'secure',
            'created_at:datetime',
            'updated_at:datetime',
            'created_by',
            'updated_by',
            'lft',
            'rgt',
            'level',
            'ordering',
            'hits',
            'lock',
        ],
    ]) ?>

</div>
