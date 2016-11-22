<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 24.09.2016
 * Project: osnovasite
 * File name: history.php
 */

//$this->params['']
?>

<section class="prjct-in clearfix">
    <div class="container-fluid history-cont">
        <?= \yii\widgets\ListView::widget([
            'summary' => false,
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'container history wow fadeInRight'],
            'itemView' => '_item_history',
            'pager' => [
                'class' => \kop\y2sp\ScrollPager::className(),
                'item' => '.history',
                'container' => '.list-view',
                'triggerText' => Yii::t('system', 'Show more ∨'),
                'noneLeftText' => '',//Yii::t('system', 'You have reached the end of the list'),
                'triggerTemplate' => '
                    <div class="container clearfix">
                        <div class="readmore-ajax">
                            <button class="btn btn-default rdmr-btn">
                                {text}
                            </button>
                        </div>
                    </div>
                    ',
                'delay' => 0
            ]
        ]); ?>
    </div>
</section>
