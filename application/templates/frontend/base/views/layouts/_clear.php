<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: _clear.php
 */

/* @var $this \app\components\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;

$bundle = \app\templates\frontend\base\assets\BaseAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<!-- Created by Volodumur Grivinskiy Design4web.biz -->
<html lang="<?= Yii::$app->language ?>">
<head>
    <base href="<?= Yii::$app->homeUrl ?>">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    echo Html::csrfMetaTags();
    $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()], 'canonical');
    $this->renderMetaTags();
    $this->head();
    ?>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="<?= implode(' ', (array)$this->bodyClass) ?>">
<?php $this->beginBody() ?>
    <div id="main">
        <?= $content ?>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
