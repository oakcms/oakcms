<?php
$this->title = Yii::t('content', 'Edit category');
?>

<?php if(!empty($this->params['submenu'])) echo $this->render('_submenu', ['model' => $model], $this->context); ?>
<?= $this->render('_form', [
    'model' => $model,
    'lang'  => $lang
]) ?>
