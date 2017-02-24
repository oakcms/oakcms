<?php

use app\modules\user\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\modules\user\models\backend\User */

$this->title = Yii::t('user', 'Update {username}', ['username' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Update');
?>
<div class="user-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
