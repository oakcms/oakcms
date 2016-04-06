<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 03.07.2015
 * Project: oakcms2
 * File name: content.php
 */

use yii\widgets\Breadcrumbs;

$this->bodyClass = 'skin-blue sidebar-collapse sidebar-mini';
?>
<?php $this->beginContent('@app/templates/backend/base/views/layouts/_clear.php'); ?>
<?= \Yii::$app->view->renderFile('@app/templates/backend/base/views/layouts/blocks/header.php'); ?>
<?= \Yii::$app->view->renderFile('@app/templates/backend/base/views/layouts/blocks/sidebar.php'); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            Sidebar Collapsed
            <small>Layout with collapsed sidebar on load</small>
        </h1>
        <?= Breadcrumbs::widget([
            'homeLink' => true,
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>

    <section class="content">
        <?php if(Yii::$app->session->hasFlash('alert')):?>
            <?= \yii\bootstrap\Alert::widget([
                'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
            ])?>
        <?php endif; ?>

        <?= $content ?>
    </section>



</div>
<?php $this->endContent() ?>
