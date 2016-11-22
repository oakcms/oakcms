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
use app\modules\system\components\Menu;
use app\modules\menu\api\Menu as ApiMenu;


$isHome = (Yii::$app->request->baseUrl.'/index' == Url::to([''])) ? true : false;
?>

<header class="clearfix">
    <div class="container header-cnt">

        <div class="container logo-nav clearfix">
            <div class="col-md-12 col-lg-3 text-center logo-cont">
                <a <?= $isHome ? '' : 'href="'.\yii\helpers\Url::to(['/system/default']).'"' ?> class="logo">
                    <img src="img/logo2.svg" alt="" width="212" height="62">
                </a>
            </div>

            <div class="col-md-12 col-lg-9 nav-all">
                <nav>
                    <h6 class="brgmenu">&nbsp;</h6>
                    <?php
                    echo Menu::widget([
                        'items' => ApiMenu::getMenuLvl(1, 0, 0),
                        'activeCssClass' => 'current',
                        'options' => ['class' => 'mainmenu text-center pull-right'],
                    ]);
                    ?>
                </nav>
            </div>
        </div>

        <div class="container back-link-numb clearfix">
            <div class="col-md-3 col-md-push-9">
                <a href="#" class="btn call">Заказать звонок</a>
                <div class="call-wrapp">
                    <p class="hidden js-thank-you">Спасибо, мы вас вот-вот наберем!</p>
                    <form action="#" method="get">
                        <label for="c-name">Имя</label>
                        <input type="text" name="name" id="c-name">
                        <label for="c-tel">Телефон</label>
                        <input type="tel" name="tel" id="c-tel">
                        <div class="error hidden"></div>
                        <input type="submit" value="Отправить">
                    </form>
                    <a href="#" class="btn close hidden js-thank-you">Закрыть</a>
                </div>
            </div>

            <div class="col-md-3 col-md-pull-3 col-md-offset-6 text-right">
                <span class="phone-number"><?= $model->settings['telephone']['value'] ?></span>
            </div>
        </div>
    </div>
</header>
