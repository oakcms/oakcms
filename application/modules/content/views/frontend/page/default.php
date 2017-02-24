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

$this->setSeoData($model->title, $model->description, '');
?>

<section class="<?= $model->slug ?>">
    <h2 class="h2-red">
        <?= $model->title ?>
    </h2>

    <div class="container services">
        <?= $model->content ?>
    </div>
</section>
