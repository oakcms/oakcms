<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\text\models\Text;
 */

use yii\helpers\Url;


$isHome = (Yii::$app->request->baseUrl.'/index' == Url::to([''])) ? true : false;
?>
<div class="block_vakansii container">
    <div class="list_vakansii">

        <!-------------- ця частина має клонуватись ---------------------->
        <div class="item">
            <h4><a href="one-visa.html">ПРОГРАММИСТЫ (SOFTWARE DEVELOPER)</a></h4>
            <div class="block_info_vakansii inline-layout">
                <div class="block-img"><a href="one-visa.html">
                        <div class="image-holder"><img src="img/content/img-vacansii.jpg" alt="ПРОГРАММИСТЫ (SOFTWARE DEVELOPER)" class="fake-img">
                            <div style="background: url('img/content/img-vacansii.jpg') no-repeat center;background-size: cover;" class="img"></div>
                        </div></a></div>
                <div class="info_vakansii">
                    <div class="row inline-layout col-2">
                        <div class="column">
                            <div class="status">статус</div>
                            <div class="indificator">Идентификатор</div>
                            <div class="pol">пол</div>
                            <div class="vozrast">возраст</div>
                            <div class="oput">опыт</div>
                            <div class="strana">страна</div>
                        </div>
                        <div class="column">
                            <div class="status hot">Горячая</div>
                            <div class="indificator">790</div>
                            <div class="pol">мужчины</div>
                            <div class="vozrast">20 - 47 лет</div>
                            <div class="oput">3 года</div>
                            <div style="background: url('img/icons/icon-flag_polsha.png') no-repeat left center" class="strana">Польша</div>
                        </div>
                    </div>
                </div>
                <div class="info_price">
                    <h4>зарплата</h4>
                    <p>PLN = 3 000 - 7 000</p>
                    <p>UAH ≈ 20 100 - 46 900</p>
                    <p>USD ≈ 747 - 1 743</p>
                    <p>EUR ≈ 663 - 1 548</p><a href="one-visa.html" class="link_more">Посмотреть детальнее ></a>
                </div>
            </div>
        </div>
        <!-------------- ця частина має клонуватись ---------------------->

    </div>
</div>
