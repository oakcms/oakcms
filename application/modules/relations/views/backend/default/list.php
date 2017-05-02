<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Html;
?>
<h3><?= Yii::t('relations', 'Select relations') ?></h3>
<div class="list-index">
    <form action="" method="post" class="oakcms-relations-search">
        <div class="form-group">
            <input type="text" class="form-control" name="s" value="<?=Html::encode(yii::$app->request->post('s'));?>" placeholder="Поиск..." />
        </div>
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    </form>
    <?php if(empty($modelList)) : ?>
        <p><?php echo Yii::t('admin', 'Not found') ?></p>
    <?php endif; ?>
    <ul class="oakcms-relations-list feeds">
        <?php foreach($modelList as $model): ?>
            <li>
                <div class="col1">
                    <div class="cont">
                        <div class="cont-col1">
                            <div class="label label-sm label-default">
                                <span><?= $model->getId(); ?></span>
                            </div>
                        </div>
                        <div class="cont-col2">
                            <div class="desc">
                                <?=$model->getName();?>
                                <?= Html::a(Yii::t('admin', 'Select') . ' <i class="fa fa-share"></i>', '#', [
                                    'data' => [
                                        'model' => $model::className(),
                                        'id' => $model->getId(),
                                        'name' => Html::encode($model->getName())
                                    ],
                                    'class' => 'label label-sm label-info oakcms-relations-variant'
                                ]); ?>
                                <?php foreach($fields as $field) : ?>
                                    <?php if($value = $model->{$field}) : ?>
                                        <p><?=$model->getAttributeLabel($field); ?>: <?=$value;?></p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
