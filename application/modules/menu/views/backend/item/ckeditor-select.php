<?php

/**
 * @var yii\web\View $this
 * @var string $CKEditor
 * @var string $CKEditorFuncNum
 * @var string $langCode
 */

?>
<h3><?= Yii::t('menu', 'Link Type') ?></h3>
<ul class="nav nav-pills nav-stacked">
    <li><a href="<?= \yii\helpers\Url::toRoute(['ckeditor-select-component', 'CKEditor' => $CKEditor, 'CKEditorFuncNum' => $CKEditorFuncNum, 'langCode' => $langCode]) ?>"><?= Yii::t('menu', 'Components') ?></a></li>
    <li><a href="<?= \yii\helpers\Url::toRoute(['ckeditor-select-menu', 'CKEditor' => $CKEditor, 'CKEditorFuncNum' => $CKEditorFuncNum, 'langCode' => $langCode]) ?>"><?= Yii::t('menu', 'Menu Items') ?></a></li>
    <li><a href="<?= \yii\helpers\Url::toRoute(['/admin/file-manager-elfinder/manager', 'CKEditor' => $CKEditor, 'CKEditorFuncNum' => $CKEditorFuncNum, 'langCode' => $langCode]) ?>"><?= Yii::t('menu', 'Media Manager') ?></a></li>
</ul>
