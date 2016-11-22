<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<div class="list-index">
    <form action="" method="post" class="pistol88-relations-search">
        <input type="text" name="s" value="<?=Html::encode(yii::$app->request->post('s'));?>" placeholder="Поиск..." />
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    </form>
    <?php if(empty($modelList)) { ?>
        <p>Not found</p>
    <?php } ?>
    <ul class="pistol88-relations-list">
        <?php foreach($modelList as $model) { ?>
            <li>
                <a href="#" data-model="<?=$model::className();?>" data-id="<?=$model->getId();?>" data-name="<?=Html::encode($model->getName());?>" class="pistol88-relations-variant ">
                    <div class="row">
                        <div class="col-lg-2 col-xs-2"><?=$model->getId();?></div>
                        <div class="col-lg-10 col-xs-10">
                            <p><?=$model->getName();?></p>
                            <?php foreach($fields as $field) { ?>
                                <?php if($value = $model->{$field}) { ?>
                                    <p><?=$model->getAttributeLabel($field); ?>: <?=$value;?></p>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>