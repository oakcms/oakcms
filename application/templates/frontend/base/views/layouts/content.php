<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\ArrayHelper;
use app\modules\text\api\Text;

$this->bodyClass[] = 'base';

?>
<?php $this->beginContent('@app/templates/frontend/base/views/layouts/_clear.php'); ?>
<?php if(Yii::$app->session->hasFlash('alert')):?>
<div class="content-wrapper">
    <section class="content">
        <?= \yii\bootstrap\Alert::widget([
            'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
            'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
        ])?>
    </section>
</div>
<?php endif; ?>
<?= $content ?>
<?php $this->endContent() ?>
