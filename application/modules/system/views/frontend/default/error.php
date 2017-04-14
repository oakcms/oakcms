<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */
use yii\helpers\Html;

/* @var $this \app\components\CoreView */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
$this->bodyClass = ['error'];

?>
<div class="height-100" style="background-color: #dcaf58">
    <div class="text-center">
        <?php
        if($exception->statusCode == 404) {
            ?>
            <div class="mb-30 font-22" style="text-transform: uppercase"><?= Yii::t('admin', 'Error 404! Page not found.') ?></div>
            <div class="line mb-20">
                <img src="uploads/page-404.png" alt="404" class="img-responsive center-block">
            </div>
            <?
        } else {
            ?>
            <div class="mb-30 font-20" style="text-transform: uppercase"><?= Html::encode($this->title) ?></div>
            <div class="line mb-20">
                <div class="font-40"><?= Html::encode($this->title) ?></div>
            </div>
            <?php
        }
        ?>
        <div class="font-22">
            <?= nl2br(Yii::t('admin', "This sometimes happens :( \n The most likely reasons for this - an outdated link or the page has been removed by the author.")) ?>
        </div>
        <div class="font-22">
            <?= Html::a(Yii::t('admin', 'Go back to the home page'), \yii\helpers\Url::to(['/'])) ?>
        </div>
    </div>
</div>
