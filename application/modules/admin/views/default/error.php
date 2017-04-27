<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 09.12.2016
 * Project: oakcms
 * File name: error.php
 *
 * @var $exception Exception
 */

$this->title = Yii::t('admin', '{errorCode} Error Page', ['errorCode' => $exception->statusCode])
?>
<section class="content">

    <div class="error-page">
        <h2 class="headline text-red"><?= $exception->statusCode ?></h2>

        <div class="error-content">
            <h3><i class="fa fa-warning text-red"></i> <?= Yii::t('admin', 'Oops! Something went wrong.') ?></h3>

            <p>
                We will work on fixing that right away.
                Meanwhile, you may <a href="<?= \yii\helpers\Url::to(['/admin/default/index']) ?>">return to dashboard</a> or try using the search form.
            </p>
        </div>
    </div>
</section>
