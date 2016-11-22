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
<footer id="contacts">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-3">
                <a href="/" class="logo">
                    <img src="img/logo2-white.svg" alt="">
                </a>
            </div>

            <div class="col-xs-12 col-sm-3 col-sm-offset-1">
                <h4>Отдел продаж</h4>

                <div class="foot-descr">
                    <p><?= $model->settings['sales_department']['value'] ?></p>

                    <p>График работы: <?= $model->settings['schedule']['value'] ?></p>

                    <span><?= $model->settings['telephone']['value'] ?></span>

                    <?= $model->settings['links']['value'] ?>
                </div>

                <a href="<?= $model->settings['facebook_link']['value'] ?>" class="fb">
                    <svg width="39px" height="39px" viewBox="0 0 39 39" version="1.1"
                         xmlns="http://www.w3.org/2000/svg">
                        <!-- Generator: sketchtool 39.1 (31720) - http://www.bohemiancoding.com/sketch -->
                        <title>225B2FE2-FD3B-46D5-8F5E-FACEF1D4D7F6</title>
                        <desc>Created with sketchtool.</desc>
                        <defs></defs>
                        <g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="footer" transform="translate(-482.000000, -414.000000)" fill="#FFFFFF">
                                <g id="Footer">
                                    <g transform="translate(140.000000, 78.000000)">
                                        <path d="M361.5,375 C350.747479,375 342,366.25241 342,355.500247 C342,344.74759 350.747479,336 361.5,336 C372.252521,336 381,344.74759 381,355.500247 C381,366.25241 372.252521,375 361.5,375 L361.5,375 Z M361.5,337.482307 C351.564714,337.482307 343.482289,345.564835 343.482289,355.500247 C343.482289,365.435165 351.564714,373.517693 361.5,373.517693 C371.434792,373.517693 379.517711,365.435165 379.517711,355.500247 C379.517711,345.564835 371.434792,337.482307 361.5,337.482307 Z M370,363.006619 C370,363.555283 369.555283,364 369.006619,364 L364.419711,364 L364.419711,357.029664 L366.7595,357.029664 L367.110076,354.312822 L364.419711,354.312822 L364.419711,352.578573 C364.419711,351.792106 364.638392,351.25619 365.766119,351.25619 L367.204707,351.25521 L367.204707,348.825693 C366.956117,348.792351 366.101986,348.718804 365.108605,348.718804 C363.034567,348.718804 361.614611,349.9848 361.614611,352.30939 L361.614611,354.312822 L359.268938,354.312822 L359.268938,357.029664 L361.614611,357.029664 L361.614611,364 L352.993381,364 C352.444717,364 352,363.555283 352,363.006619 L352,346.993381 C352,346.444717 352.444717,346 352.993381,346 L369.006619,346 C369.555283,346 370,346.444717 370,346.993381 L370,363.006619 Z"
                                              id="icon_fb"></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </a>
            </div>

            <div class="col-xs-12 col-sm-4 col-sm-offset-1">
                <div>
                    <h4>Форма обратной связи</h4>
                    <p class="ok hidden">Ваш запрос получен!<br>Мы свяжемся с вами в ближайшее время!</p>
                    <form class="formQuest" method='post' autocomplete="off">
                        <label for="name">Имя</label>
                        <input type="text" name="name" id="name" pattern="[А-Яа-яA-Za-z]{3,30}" required>
                        <label for="tel">Телефон</label>
                        <input type="tel" name="tel" id="tel" required>
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                        <label for="comment">Комментарий</label>
                        <textarea name="comment" id="comment"></textarea>
                        <input type="hidden" name="form" value="Замовлення зворотнього звяку!">
                        <input type="submit" name="send" value="Отправить">
                    </form>
                </div>
            </div>
        </div>
    </div>
</footer>
