<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;
use app\modules\content\models\ContentCategory;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\content\models\search\ContentCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('content', 'Content Categories');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName' => 'a',
        'label' => Yii::t('content', 'Create'),
        'options' => [
            'href' => Url::to(['create'])
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
    ],
    [
        'label' => Yii::t('app', 'Control'),
        'options' => [
        'class' => 'btn blue btn-outline btn-circle btn-sm',
        'data-hover' => "dropdown",
        'data-close-others' => "true",
    ],
    'dropdown' => [
        'options' => ['class' => 'pull-right'],
        'encodeLabels' => false,
        'items' => [
                [
                    'label' => '<span class="font-red"><i class="fa fa-trash-o"></i> ' . Yii::t('app', 'Delete') . '</span>',
                    'url' => 'javascript:void(0)',
                        'linkOptions' => [
                        'onclick' => 'deleteA()',
                    ]
                ],
                [
                    'label' => '<span class="font-green-turquoise"><i class="fa fa-toggle-on"></i> ' . Yii::t('app', 'Published') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'publishedA()']
                ],
                [
                    'label' => '<span class="font-blue-chambray"><i class="fa fa-toggle-off"></i> ' . Yii::t('app', 'Unpublished') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'unpublishedA()']
                ],
            ],
        ],
    ]
];
?>
<div class="content-category-index">
    <?php if(sizeof($cats) > 0) : ?>
        <table class="table table-hover">
            <tbody>
            <?php foreach($cats as $cat) : ?>
                <tr>
                    <td width="50"><?= $cat->category_id ?></td>
                    <td style="padding-left:  <?= $cat->depth * 20 ?>px;">
                        <?php if(count($cat->children)) : ?>
                            <i class="caret"></i>
                        <?php endif; ?>
                        <?php if(!count($cat->children) || !empty(Yii::$app->controller->module->settings['itemsInFolder'])) : ?>
                            <a href="<?= Url::to([$baseUrl . $this->context->viewRoute, 'id' => $cat->category_id]) ?>" <?= ($cat->status == CategoryModel::STATUS_OFF ? 'class="smooth"' : '') ?>><?= $cat->title ?></a>
                        <?php else : ?>
                            <span <?= ($cat->status == CategoryModel::STATUS_OFF ? 'class="smooth"' : '') ?>><?= $cat->title ?></span>
                        <?php endif; ?>
                    </td>
                    <td width="120" class="text-right">
                        <div class="dropdown actions">
                            <i id="dropdownMenu<?= $cat->category_id ?>" data-toggle="dropdown" aria-expanded="true" title="<?= Yii::t('easyii', 'Actions') ?>" class="glyphicon glyphicon-menu-hamburger"></i>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu<?= $cat->category_id ?>">
                                <li><a href="<?= Url::to([$baseUrl.'/a/edit', 'id' => $cat->category_id]) ?>"><i class="glyphicon glyphicon-pencil font-12"></i> <?= Yii::t('easyii', 'Edit') ?></a></li>
                                <li><a href="<?= Url::to([$baseUrl.'/a/create', 'parent' => $cat->category_id]) ?>"><i class="glyphicon glyphicon-plus font-12"></i> <?= Yii::t('easyii', 'Add subcategory') ?></a></li>
                                <li role="presentation" class="divider"></li>
                                <li><a href="<?= Url::to([$baseUrl.'/a/up', 'id' => $cat->category_id]) ?>"><i class="glyphicon glyphicon-arrow-up font-12"></i> <?= Yii::t('easyii', 'Move up') ?></a></li>
                                <li><a href="<?= Url::to([$baseUrl.'/a/down', 'id' => $cat->category_id]) ?>"><i class="glyphicon glyphicon-arrow-down font-12"></i> <?= Yii::t('easyii', 'Move down') ?></a></li>
                                <li role="presentation" class="divider"></li>
                                <?php if($cat->status == CategoryModel::STATUS_ON) :?>
                                    <li><a href="<?= Url::to([$baseUrl.'/a/off', 'id' => $cat->category_id]) ?>" title="<?= Yii::t('easyii', 'Turn Off') ?>'"><i class="glyphicon glyphicon-eye-close font-12"></i> <?= Yii::t('easyii', 'Turn Off') ?></a></li>
                                <?php else : ?>
                                    <li><a href="<?= Url::to([$baseUrl.'/a/on', 'id' => $cat->category_id]) ?>" title="<?= Yii::t('easyii', 'Turn On') ?>"><i class="glyphicon glyphicon-eye-open font-12"></i> <?= Yii::t('easyii', 'Turn On') ?></a></li>
                                <?php endif; ?>
                                <li><a href="<?= Url::to([$baseUrl.'/a/delete', 'id' => $cat->category_id]) ?>" class="confirm-delete" data-reload="1" title="<?= Yii::t('easyii', 'Delete item') ?>"><i class="glyphicon glyphicon-remove font-12"></i> <?= Yii::t('easyii', 'Delete') ?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?= Yii::t('admin', 'No records found') ?></p>
    <?php endif; ?>
</div>
<script>
    function deleteA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['/admin/content/category/delete-ids']) ?>' + '?id=' + keys.join();
    }
    function publishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['/admin/content/category/published']) ?>' + '?id=' + keys.join();
    }
    function unpublishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['/admin/content/category/unpublished']) ?>' + '?id=' + keys.join();
    }
</script>
