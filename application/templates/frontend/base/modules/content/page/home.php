<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 21.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\content\models\ContentPages;
 */


$this->bodyClass = ['page-'.$model->id];

$meta_title = ($model->meta_title != '') ? $model->meta_title : $model->title;
$meta_description = \app\helpers\StringHelper::truncate((($model->meta_description != '') ? $model->meta_description : strip_tags($model->description)), '140', '');
$meta_keywords = ($model->meta_keywords != '') ? $model->meta_keywords : implode(', ', explode(' ', $model->title));

$this->setSeoData($meta_title, $meta_description, $meta_keywords);

?>

<section class="<?= $model->slug ?> block_about">
    <h2><?= $model->title ?></h2>
    <?= $model->content ?>
</section>
