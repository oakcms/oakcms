<?php

use yii\helpers\Url;
use app\widgets\grid\ActionColumn;
use app\widgets\grid\LinkColumn;
use app\widgets\grid\SetColumn;
use app\modules\user\models\backend\User;
use app\modules\user\widgets\backend\grid\RoleColumn;
use kartik\date\DatePicker;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'Users');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName' => 'a',
        'label' => Yii::t('user', 'Create'),
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
];
?>
<div class="user-index">

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                [
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'date_from',
                        'attribute2' => 'date_to',
                        'type' => DatePicker::TYPE_RANGE,
                        'separator' => '-',
                        'pluginOptions' => ['format' => 'yyyy-mm-dd']
                    ]),
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                    'filterOptions' => [
                        'style' => 'max-width: 180px',
                    ],
                ],
                [
                    'class' => LinkColumn::className(),
                    'attribute' => 'username',
                ],
                'email:email',
                [
                    'class' => SetColumn::className(),
                    'filter' => User::getStatusesArray(),
                    'attribute' => 'status',
                    'name' => 'statusName',
                    'cssCLasses' => [
                        User::STATUS_ACTIVE => 'success',
                        User::STATUS_WAIT => 'warning',
                        User::STATUS_BLOCKED => 'default',
                    ],
                ],
                [
                    'class' => RoleColumn::className(),
                    'filter' => \yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description'),
                    'attribute' => 'role',
                ],
                ['class' => ActionColumn::className()],
            ],
        ]); ?>
    </div>
</div>
<script>
    function deleteA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/seo/delete-ids?id=' + keys.join();
    }
    function publishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/seo/published?id=' + keys.join();
    }
    function unpublishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/seo/unpublished?id=' + keys.join();
    }
</script>
