<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: _clear.php
 */

/* @var $this \app\components\AdminView */
/* @var $content string */

use yii\helpers\Html;

$bundle = \app\templates\backend\base\assets\BaseAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <base href="<?= Yii::$app->homeUrl ?>">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <link rel="shortcut icon" href="<?= $bundle->baseUrl ?>/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= $bundle->baseUrl ?>/favicon.ico" type="image/x-icon">

    <?php $this->head() ?>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<?= Html::beginTag('body', ['class' => 'skin-blue sidebar-mini fixed '.implode(' ', (array)$this->bodyClass)]) ?>
<?php $this->beginBody() ?>
    <div class="wrapper">
        <?= $content ?>
    </div>
<?php $this->endBody() ?>
<?php foreach(Yii::$app->session->getAllFlashes() as $key => $message) : ?>
    <?if(isset($message)):?>
        <div id="growl_text_<?= $key ?>" class="hidden"><?= $message ?></div>
        <script>
            $(function() {
                $.bootstrapGrowl($('#growl_text_<?= $key ?>').html(), {
                    ele: 'body',
                    type: '<?=str_replace("alert-", "", $key) ?>',
                    offset: {
                        from: 'bottom',
                        amount: 10
                    },
                    align: 'right',
                    width: 'auto',
                    delay: 5000,
                    allow_dismiss: true,
                    stackup_spacing: 10
                });
            });
        </script>
    <?endif?>
<?php endforeach; ?>
<?= Html::endTag('body') ?>
</html>
<?php $this->endPage() ?>
