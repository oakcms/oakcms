<?php
$this->title = Yii::t('content', 'Create category');
?>

<?= $this->render('_form', [
    'model' => $model,
    'parent' => $parent,
    'lang'  => $lang
]) ?>
