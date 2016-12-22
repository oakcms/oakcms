<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.12.2016
 * Project: oakcms
 * File name: view.php
 *
 * @var $this \app\components\CoreView;
 * @var $model \app\modules\shop\models\Category;
 */

use yii\helpers\Html;

?>

<div class="actions">
    <?= Html::tag('h3', Yii::t('shop', 'Акции')) ?>
    <hr class="border">
    <div class="row">
        <?php
        /** @var \app\modules\shop\models\Product $action */
        foreach($this->context->module->getService('product')->getActionProducts() as $action):?>
        <div class="col-md-3 col-sm-6">
            <div class="one_product">
                <?= Html::a(Html::img($action->getImage()->getUrl()), ['/shop/product/view', 'slug' => $action->slug]) ?>
                <span class="title"><?= $action->name ?></span><br>

                <span class="price"><?= $action->getPrice() ?> P</span>
                <button class="btn btn_by">купить</button>
            </div>
        </div>
        <?endforeach;?>
    </div>
    <hr class="border">
</div>
