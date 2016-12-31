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
 * @var $productDataProvider \yii\data\ActiveDataProvider;
 */

use yii\helpers\Html;

?>

<h1 class="title text-center"><?= $model->name ?></h1>
<?if($model->text != ''):?>
<div class="descr_page">
    <?= $model->text ?>
</div>
<?endif;?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9 col-md-push-3">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $productDataProvider,
                'itemView' => '_item',
                'summary' => false,
            ]); ?>
        </div>
        <div class="col-md-3 col-md-pull-9">
            <div class="filter_left_sidebar">

                <span class="filter_name">Геметрия</span>
                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="Подсказка"></button>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="">
                        Прямая
                    </label>

                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="">
                        Угловая
                    </label>

                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="">
                        П-образная
                    </label>

                </div>

                <div class="line "></div>

                <span class="filter_name">Стиль</span>
                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="Подсказка">  </button>

                <select class="form-control">
                    <option>Первый </option>
                    <option>Второй</option>
                    <option>Третий</option>
                </select>

                <div class="line"></div>


                <div class="scroll_slider">
                    <span class="filter_name">Цена</span>

                    <form class="form-inline" role="form">
                        <div class="form-group">
                            <input type="text" id="amount" class="form-control" value="от" style="margin-right: 10px;">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" value="до" id="amount_2">
                        </div>
                    </form>

                    <div id="slider-range" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content"><div class="ui-slider-range ui-corner-all ui-widget-header" style="left: 15%; width: 45%;"></div><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 15%;"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 60%;"></span></div>
                </div>


                <div class="line"></div>

                <span class="filter_name">Габариты</span>
                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="Подсказка">  </button>
                <input type="text">

                <div class="line"></div>

                <span class="filter_name">Цвет</span>
                <select class="form-control">
                    <option></option>
                    <option>Первый </option>
                    <option>Второй</option>
                    <option>Третий</option>
                </select>

                <div class="line"></div>

                <span class="filter_name">Площадь кухни,кв.м.</span>
                <select class="form-control">
                    <option></option>
                    <option>Первый </option>
                    <option>Второй</option>
                    <option>Третий</option>
                </select>

                <div class="line"></div>

                <span class="filter_name">Декор</span>
                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="Подсказка">  </button>

                <select class="form-control">
                    <option></option>
                    <option>Первый </option>
                    <option>Второй</option>
                    <option>Третий</option>
                </select>
                <div class="line"></div>


                <span class="filter_name">Производитель</span>
                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="Предприятие изготовитель даной линейки мебели"> </button>

                <select class="form-control">
                    <option></option>
                    <option>Первый </option>
                    <option>Второй</option>
                    <option>Третий</option>
                </select>

                <div class="line"></div>

                <a href="#">Сбросить параметры</a>
            </div>
        </div>
    </div>
</div>

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
