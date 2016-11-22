<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: _clear.php
 */

/* @var $this \app\components\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;

$bundle = \app\templates\frontend\base\assets\BaseAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<!-- Created by Volodumur Grivinskiy Design4web.biz -->
<html lang="<?= Yii::$app->language ?>">
<head>
    <base href="<?= Yii::$app->homeUrl ?>">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    echo Html::csrfMetaTags();
    $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()], 'canonical');
    $this->renderMetaTags();
    $this->head();
    ?>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="<?= implode(' ', (array)$this->bodyClass) ?>">
    <!-- Google Tag Manager -->
    <script>
        (function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-K6BTF9');
    </script>
    <!-- End Google Tag Manager -->
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6BTF9" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

<?php $this->beginBody() ?>
    <div id="main">
        <?= $content ?>
    </div>
    <div class="popups">
        <div class="container hidden" id="room-1">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/1/type1/19_0.jpg" alt="Room Photo">

                        <h3>1 ком тип 1.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                                <span>Гардеробная</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>59,5 кв.м</span>
                                <span>24,9 кв.м</span>
                                <span>14,2 кв.м</span>
                                <span>8,4 кв.м</span>
                                <span>5 кв.м</span>
                                <span>2,6 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/1/type1/19_1-(1).jpg" alt="Room Photo">

                        <h3>1 ком тип 1.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                                <span>Гардеробная</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>59,5 кв.м</span>
                                <span>24,9 кв.м</span>
                                <span>14,2 кв.м</span>
                                <span>8,4 кв.м</span>
                                <span>5 кв.м</span>
                                <span>2,6 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>

                    <div class="col-xs-12">
                        <!-- p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/1.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="#" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="1 ком тип 1">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container hidden" id="room-2">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/1/type2/1-k-49-cut.jpg" alt="Room Photo">

                        <h3>1 ком тип 2.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната </span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>49 кв.м</span>
                                <span>20,4 кв.м</span>
                                <span>11,9 кв.м</span>
                                <span>10,2 кв.м</span>
                                <span>4,5 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/1/type2/1-k-49-cut-per-2.jpg" alt="Room Photo">

                        <h3>1 ком тип 2.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната </span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>49 кв.м</span>
                                <span>20,4 кв.м</span>
                                <span>11,9 кв.м</span>
                                <span>10,2 кв.м</span>
                                <span>4,5 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>
                    <div class="col-xs-12">
                        <!--p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/2.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="#" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="1 ком тип 2">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container hidden" id="room-3">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/2/type1/370_0.jpg" alt="Room Photo">

                        <h3>2 ком тип 1.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>80 кв.м</span>
                                <span>23,4 кв.м</span>
                                <span>15,6 кв.м</span>
                                <span>14,5 кв.м</span>
                                <span>19,0 кв.м</span>
                                <span>6,0 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/2/type1/370_1.jpg" alt="Room Photo">

                        <h3>2 ком тип 1.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>80 кв.м</span>
                                <span>23,4 кв.м</span>
                                <span>15,6 кв.м</span>
                                <span>14,5 кв.м</span>
                                <span>19,0 кв.м</span>
                                <span>6,0 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>
                    <div class="col-xs-12">
                        <!-- p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/3-1.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="#" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="2 ком тип 1">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container hidden" id="room-4">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/2/type2/2-k-75-gen.jpg" alt="Room Photo">

                        <h3>2 ком тип 2.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>75,0 кв.м</span>
                                <span>18,5 кв.м</span>
                                <span>21,1 кв.м</span>
                                <span>12,6 кв.м</span>
                                <span>14,6 кв.м</span>
                                <span>6,4 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/2/type2/2-k-75-per-3.jpg" alt="Room Photo">

                        <h3>2 ком тип 2.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>75,0 кв.м</span>
                                <span>18,5 кв.м</span>
                                <span>21,1 кв.м</span>
                                <span>12,6 кв.м</span>
                                <span>14,6 кв.м</span>
                                <span>6,4 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>
                    <div class="col-xs-12">
                        <!-- p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/3.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="img/plans/4.png" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="2 ком тип 2">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container hidden" id="room-5">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/2/type3/2-k-85.jpg" alt="Room Photo">

                        <h3>2 ком тип 3.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>85 кв.м</span>
                                <span>18,5 кв.м</span>
                                <span>21,1 кв.м</span>
                                <span>12,6 кв.м</span>
                                <span>14,6 кв.м</span>
                                <span>6,4 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/2/type3/2-k-86-per.jpg" alt="Room Photo">

                        <h3>2 ком тип 3.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>85 кв.м</span>
                                <span>18,5 кв.м</span>
                                <span>21,1 кв.м</span>
                                <span>12,6 кв.м</span>
                                <span>14,6 кв.м</span>
                                <span>6,4 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>
                    <div class="col-xs-12">
                        <!-- p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/4.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="#" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="2 ком тип 3">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container hidden" id="room-6">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/3/type1/3-k-107.jpg" alt="Room Photo">

                        <h3>3 ком тип 1.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Комната 3</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел 1</span>
                                <span>Санузел 2</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>105,0 кв.м</span>
                                <span>17,6 кв.м</span>
                                <span>15,6 кв.м</span>
                                <span>22,3 кв.м</span>
                                <span>16,0 кв.м</span>
                                <span>24,4 кв.м</span>
                                <span>5,1 кв.м</span>
                                <span>1,9 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/3/type1/3-k-107-per.jpg" alt="Room Photo">

                        <h3>3 ком тип 1.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Комната 3</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел 1</span>
                                <span>Санузел 2</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>105,0 кв.м</span>
                                <span>17,6 кв.м</span>
                                <span>15,6 кв.м</span>
                                <span>22,3 кв.м</span>
                                <span>16,0 кв.м</span>
                                <span>24,4 кв.м</span>
                                <span>5,1 кв.м</span>
                                <span>1,9 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>
                    <div class="col-xs-12">
                        <!-- p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/5.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="img/plans/6.png" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="3 ком тип 1">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container hidden" id="room-7">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/4/type1/001.jpg" alt="Room Photo">

                        <h3>3 и более комнат тип 1.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Комната 3</span>
                                <span>Кухня</span>
                                <span>Прихожая 1</span>
                                <span>Прихожая 2</span>
                                <span>Санузел 1</span>
                                <span>Санузел 2</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>92,0 кв.м</span>
                                <span>18,4 кв.м</span>
                                <span>14,5 кв.м</span>
                                <span>13,0 кв.м</span>
                                <span>13,1 кв.м</span>
                                <span>8,9 кв.м</span>
                                <span>8,7 кв.м</span>
                                <span>5,0 кв.м</span>
                                <span>5,0 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/4/type1/002.jpg" alt="Room Photo">

                        <h3>3 и более комнат тип 1.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Комната 3</span>
                                <span>Кухня</span>
                                <span>Прихожая 1</span>
                                <span>Прихожая 2</span>
                                <span>Санузел 1</span>
                                <span>Санузел 2</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>92,0 кв.м</span>
                                <span>18,4 кв.м</span>
                                <span>14,5 кв.м</span>
                                <span>13,0 кв.м</span>
                                <span>13,1 кв.м</span>
                                <span>8,9 кв.м</span>
                                <span>8,7 кв.м</span>
                                <span>5,0 кв.м</span>
                                <span>5,0 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>
                    <div class="col-xs-12">
                        <!-- p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/6.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="img/plans/7.png" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="3 и более комнат тип 1">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container hidden" id="room-8">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/4/type2/001.jpg" alt="Room Photo">

                        <h3>3 и более комнат тип 2.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Комната 3</span>
                                <span>Комната 4</span>
                                <span>Комната 5</span>
                                <span>Комната 6</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>179,2 кв.м</span>
                                <span>16,7 кв.м</span>
                                <span>15,5 кв.м</span>
                                <span>21,9 кв.м</span>
                                <span>21,1 кв.м</span>
                                <span>26 кв.м</span>
                                <span>18,7 кв.м</span>
                                <span>15,9 кв.м</span>
                                <span>24,5 кв.м</span>
                                <span>5,1 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/4/type2/002.jpg" alt="Room Photo">

                        <h3>3 и более комнат тип 2.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Комната 3</span>
                                <span>Комната 4</span>
                                <span>Комната 5</span>
                                <span>Комната 6</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>179,2 кв.м</span>
                                <span>16,7 кв.м</span>
                                <span>15,5 кв.м</span>
                                <span>21,9 кв.м</span>
                                <span>21,1 кв.м</span>
                                <span>26 кв.м</span>
                                <span>18,7 кв.м</span>
                                <span>15,9 кв.м</span>
                                <span>24,5 кв.м</span>
                                <span>5,1 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>
                    <div class="col-xs-12">
                        <!-- p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/7.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="img/plans/8.png" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="3 и более комнат тип 2">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container hidden" id="room-9">
            <div class="row">
                <div class="rooms">
                    <div class="col-sm-6">
                        <h3>План квартиры</h3>

                        <img src="img/plans/4/type3/001.jpg" alt="Room Photo">

                        <h3>3 и более комнат тип 3.1.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Комната 3</span>
                                <span>Комната 4</span>
                                <span>Комната 5</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>146,2 кв.м</span>
                                <span>18,4 кв.м</span>
                                <span>21,6 кв.м</span>
                                <span>19,4 кв.м</span>
                                <span>30,2 кв.м</span>
                                <span>15,2 кв.м</span>
                                <span>12,5 кв.м</span>
                                <span>14,4 кв.м</span>
                                <span>6,5 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Вариант перепланировки</h3>

                        <img src="img/plans/4/type3/002.jpg" alt="Room Photo">

                        <h3>3 и более комнат тип 3.2.</h3>

                        <div class="descr">
                            <div class="options text-left">
                                <span>Общая площадь</span>
                                <span>Комната 1</span>
                                <span>Комната 2</span>
                                <span>Комната 3</span>
                                <span>Комната 4</span>
                                <span>Комната 5</span>
                                <span>Кухня</span>
                                <span>Прихожая</span>
                                <span>Санузел</span>
                            </div>
                            <div class="def text-center">
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                                <span>&mdash;</span>
                            </div>
                            <div class="params text-right">
                                <span>146,2 кв.м</span>
                                <span>18,4 кв.м</span>
                                <span>21,6 кв.м</span>
                                <span>19,4 кв.м</span>
                                <span>30,2 кв.м</span>
                                <span>15,2 кв.м</span>
                                <span>12,5 кв.м</span>
                                <span>14,4 кв.м</span>
                                <span>6,5 кв.м</span>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both; margin: 0!important; padding: 0!important;"></div>
                    <div class="col-xs-12">
                        <!-- p class="r-descr">Квартира расположена на 3-23 этажах Секции 1. Моря не видно, но Оболонь просматривается.</p -->
                    </div>

                    <div class="col-xs-12 text-left footer-popup">
                        <a href="img/plans/8.png" class="btn download" download>Скачать</a>
                        <div class="ok hidden">Спасибо за запрос. Вот-вот вас наберем!</div>
                        <form action="#" method="GET">
                            <input type="tel" placeholder="Телефон" name="tel">
                            <input type="hidden" name="popup" value="3 и более комнат тип 3">
                            <button>Узнать доступность</button>
                        </form>

                        <p class="contact">Отдел продаж <span>+38 (067) 323-78-07</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->endBody() ?>
    <!-- этот код вставляется перед закрывающим тегом </BODY> -->
    <script type="text/javascript" charset="utf-8" src="//istat24.com.ua/js/replace.js"></script>
    <script type="text/javascript">doReplaceIstat(1011);</script>
    <!-- конец кода который вставляется перед закрывающим тегом </BODY> -->
</body>
</html>
<?php $this->endPage() ?>
