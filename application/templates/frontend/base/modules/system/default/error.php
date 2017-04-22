<?php
/**
 * @var $exception
 * @var $this \app\components\CoreView
 */
use yii\helpers\Html;

if($exception->statusCode == 404):
$this->title = Yii::t('system', 'The requested page does not exist.');
$this->setSeoData($this->title);
?>
<div class="container">
    <div class="block_error inline-layout">
        <div class="number_error">
            <img src="images/bg/error404.png" alt="404 Оошибка">
        </div>
        <div class="text_error">
            <h3>УПС! Мы не можем найти страницу,<br>которую вы ищите</h3>
            <p>Возможно адрес страницы введен не верно или такой страницы не существует</p><a href="<?= \yii\helpers\Url::home() ?>" class="back_home">перейти на  Главную</a>
        </div>
    </div>
</div>
<?php else: ?>
<div class="container">
    <div class="block_error inline-layout">
        <div class="mb-30 font-20" style="text-transform: uppercase"><?= Html::encode($this->title) ?></div>
        <div class="line mb-20">
            <div class="font-40"><?= Html::encode($this->title) ?></div>
        </div>
    </div>
</div>
<?php endif; ?>
