<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use app\modules\admin\widgets\Button;
use app\modules\shop\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;

\app\modules\shop\assets\BackendAsset::register($this);

$this->params['actions_buttons'] = [
    [
        'tagName'      => 'a',
        'label'        => Yii::t('admin', 'Create'),
        'options'      => [
            'href' => Url::to(['create']),
        ],
        'icon'         => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size'         => Button::SIZE_SMALL,
        'disabled'     => false,
        'block'        => false,
        'type'         => Button::TYPE_CIRCLE,
    ],
    [
        'tagName'      => 'a',
        'label'        => Yii::t('admin', 'Tree'),
        'options'      => [
            'href'   => Url::toRoute(['index', 'view' => 'tree']),
            'class'  => 'btn btn-circle btn-sm' . ((Yii::$app->request->get('view') == 'tree' | Yii::$app->request->get('view') == '') ? ' blue' : ''),
        ],
        'icon'         => 'fa fa-tree',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size'         => Button::SIZE_SMALL,
        'disabled'     => false,
        'block'        => false,
        'type'         => Button::TYPE_CIRCLE,
    ],
    [
        'tagName'      => 'a',
        'label'        => Yii::t('admin', 'List'),
        'options'      => [
            'href'   => Url::toRoute(['index', 'view' => 'list']),
            'class'  => 'btn btn-circle btn-sm' . ((Yii::$app->request->get('view') == 'list') ? ' blue' : ''),
        ],
        'icon'         => 'fa fa-list',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size'         => Button::SIZE_SMALL,
        'disabled'     => false,
        'block'        => false,
        'type'         => Button::TYPE_CIRCLE,
    ]
];
?>
<div class="category-index">
    <?php
    if (isset($_GET['view']) && $_GET['view'] == 'list') {
        $categories = \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'id',
                    'filter' => false,
                    'options' => ['style' => 'width: 55px;']
                ],
                'name',
                [
                    'attribute' => 'image',
                    'format'    => 'image',
                    'filter'    => false,
                    'content'   => function ($image) {
                        if ($image = $image->getImage()->getUrl('50x50')) {
                            return "<img src=\"{$image}\" class=\"thumb\" />";
                        }
                    },
                ],
                [
                    'attribute' => 'parent_id',
                    'filter'    => Html::activeDropDownList(
                        $searchModel,
                        'parent_id',
                        Category::buildTextTree(),
                        ['class' => 'form-control', 'prompt' => 'Категория']
                    ),
                    'value'     => 'parent.name',
                ],
                ['class' => 'app\modules\admin\components\grid\ActionColumn'],
            ],
        ]);
    } else {
        $categories = \app\modules\tree\widgets\Tree::widget(['model' => new \app\modules\shop\models\Category()]);
    }

    echo $categories;
    ?>

</div>
