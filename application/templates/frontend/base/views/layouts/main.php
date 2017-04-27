<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/** @var $this \app\components\CoreView */

use app\modules\text\api\Text;

$bundle = \app\templates\frontend\base\assets\BaseAsset::register($this);
$this->beginContent('@app/templates/frontend/base/views/layouts/content.php'); ?>

<?php echo Text::get('header') ?>

<?php echo Text::get('position_top_1'); ?>
<?php echo Text::get('position_top_2'); ?>
<?php echo Text::get('position_top_3'); ?>
<?php echo Text::get('position_top_4'); ?>
<?php echo Text::get('position_top_5'); ?>

<div class="container">
    <?php if(isset($this->pageTitle) && $this->pageTitle != ''):?>
        <?php echo \yii\helpers\Html::tag($this->pageTitleHeading, $this->pageTitle) ?>
    <?php endif; ?>

    <?php if(isset($this->params['breadcrumbs'])): ?>
        <div class="breadcrumbs">
            <?php echo \yii\widgets\Breadcrumbs::widget([
                'options' => ['class' => 'inline-layout'],
                'itemTemplate' => "<li>{link}</li>\n",
                'activeItemTemplate' => "<li><span>{link}</span></li>\n",
                'links' => $this->params['breadcrumbs'] + ['label' => $this->title],
            ]); ?>
        </div>
    <?php endif; ?>
    <?php echo $content ?>
</div>

<?= Text::get('position_bottom_1'); ?>
<?= Text::get('position_bottom_2'); ?>
<?= Text::get('position_bottom_3'); ?>
<?= Text::get('position_bottom_4'); ?>
<?= Text::get('position_bottom_5'); ?>

<footer class="footer">
    <div class="container">
        <p class="pull-left"><?php echo Text::get('footer'); ?></p>
        <p class="pull-right"><?php echo \app\modules\system\Module::powered() ?></p>
    </div>
</footer>

<?php $this->endContent() ?>

