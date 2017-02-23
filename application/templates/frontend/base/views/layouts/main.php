<?php

use app\modules\text\api\Text;

$bundle = \app\templates\frontend\base\assets\BaseAsset::register($this);
$this->beginContent('@app/templates/frontend/base/views/layouts/content.php'); ?>
<?= $this->renderFile('@app/templates/frontend/base/views/layouts/blocks/header.php', ['assets'=>$bundle])?>
<?php if(isset($this->params['breadcrumbs'])): ?>
    <div class="breadcrumbs">
        <div class="container">
            <?= \yii\widgets\Breadcrumbs::widget([
                //'homeLink' => false,
                'itemTemplate' => "<li>{link}<span>&nbsp;/&nbsp;</span></li>\n",
                'links' => $this->params['breadcrumbs'],
            ]);?>
        </div>
    </div>
<?php endif; ?>
<?= $this->renderFile('@app/templates/frontend/base/views/layouts/blocks/slider.php', ['assets'=>$bundle])?>


<?= Text::get('position_top_1'); ?>
<?= Text::get('position_top_2'); ?>
<?= Text::get('position_top_3'); ?>
<?= Text::get('position_top_4'); ?>
<?= Text::get('position_top_5'); ?>

<?= $content ?>

<?= Text::get('position_bottom_1'); ?>
<?= Text::get('position_bottom_2'); ?>
<?= Text::get('position_bottom_3'); ?>
<?= Text::get('position_bottom_4'); ?>
<?= Text::get('position_bottom_5'); ?>

<?= $this->renderFile('@app/templates/frontend/base/views/layouts/blocks/footer.php', ['assets'=>$bundle])?>
<?php $this->endContent() ?>

