<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 21.12.2016
 * Project: oakcms
 * File name: view.php
 *
 * @var $this \app\components\CoreView;
 * @var $model \app\modules\shop\models\Product;
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->bodyClass[] = 'product_page';

$this->params['breadcrumbs'][] = [
    'url' => ['/shop/category/view', 'slug' => $model->category->slug],
    'label' => $model->category->name
];
$this->params['breadcrumbs'][] = $model->name;
?>

<div class="title text-center">
    <span><?= $model->name ?></span>
</div>
<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="block_header ">

            <div class="manufacturer text-left">
                <img src="img/manufactured.png" alt="">
                Производитель: <?= Html::a($model->producer->name, ['/shop/producer/view', 'slug' => $model->producer->slug]) ?>
            </div>

            <div class="question text-right">
                <img src="img/question.png" alt="" style="height: 20px;">
                <a href="#">Задать вопрос по этому товару</a>
            </div>
        </div>


    </div>

    <div class="col-md-6 col-sm-12  ">
        <div class="block_header row ">
            <div class=" article col-md-2 col-sm-12 no_padding">
                Aртикул: <span><?= $model->code ?></span>
            </div>
            <div class=" rating text-center col-sm-12 col-md-7">
                Рейтинг:
                <ul class="list-inline list-unstyled">
                    <li><img src="img/stars.png" alt=""></li>
                    <li><img src="img/stars.png" alt=""></li>
                    <li><img src="img/stars.png" alt=""></li>
                    <li><img src="img/stars.png" alt=""></li>
                    <li><img src="img/stars.png" alt=""></li>
                </ul>
                (<span>10</span> голосов)




            </div>

            <div class=" text-right col-md-3 col-sm-12 garanty no_padding ">
                Гарантия 12 месяцев
            </div>
        </div>

    </div>
</div>

<?= $model->text ?>
