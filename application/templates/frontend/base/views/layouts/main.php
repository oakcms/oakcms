<?php

/** @var $this \app\components\CoreView */

use app\modules\text\api\Text;

$bundle = \app\templates\frontend\base\assets\BaseAsset::register($this);
$this->beginContent('@app/templates/frontend/base/views/layouts/content.php'); ?>

<?= Text::get('header') ?>

<?= $this->renderFile('@app/templates/frontend/base/views/layouts/blocks/slider.php', ['assets'=>$bundle])?>
<?= $this->renderFile('@app/templates/frontend/base/views/layouts/blocks/vacancy.php', ['assets'=>$bundle])?>
<?= $this->renderFile('@app/templates/frontend/base/views/layouts/blocks/fixed_right_bord_phone.php', ['assets'=>$bundle])?>

<?= Text::get('position_top_1'); ?>
<?= Text::get('position_top_2'); ?>
<?= Text::get('position_top_3'); ?>
<?= Text::get('position_top_4'); ?>
<?= Text::get('position_top_5'); ?>
<div class="container">
    <?php if(isset($this->pageTitle) && $this->pageTitle != ''):?>
        <?php echo \yii\helpers\Html::tag($this->pageTitleHeading, $this->pageTitle) ?>
    <?php endif; ?>

    <?php if(isset($this->params['breadcrumbs'])): ?>
        <div class="breadcrumbs">
            <?= \yii\widgets\Breadcrumbs::widget([
                'options' => ['class' => 'inline-layout'],
                'itemTemplate' => "<li>{link}</li>\n",
                'activeItemTemplate' => "<li><span>{link}</span></li>\n",
                'links' => $this->params['breadcrumbs'] + ['label' => $this->title],
            ]);?>
        </div>
    <?php endif; ?>
    <?= $content ?>
</div>

<?= Text::get('position_bottom_1'); ?>
<?= Text::get('position_bottom_2'); ?>
<?= Text::get('position_bottom_3'); ?>
<?= Text::get('position_bottom_4'); ?>
<?= Text::get('position_bottom_5'); ?>

<?= $this->renderFile('@app/templates/frontend/base/views/layouts/blocks/footer.php', ['assets'=>$bundle])?>
<?php $this->endContent() ?>

