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

<header class="home">
    <div id="video_background">
        <video autoplay="" muted="" loop="">
            <source src="<?= $model->getSetting('backgroundVideo') ?>" type="video/mp4">
        </video>
    </div>
    <div class="container">
        <div data-wow-delay="2s" class="logo wow fadeIn">
            <a <?= $isHome ? '' : 'href="'.\yii\helpers\Url::to(['/']).'"' ?> ><?= $model->getSetting('logoTitle') ?>
                <span><?= $model->getSetting('logoDescription') ?></span>
            </a>
        </div>
        <nav>
            <?php
            $items = ApiMenu::getMenuLvl($model->getSetting('menu'), 1, 1);
            array_splice($items, 5);
            if(count($items) > 0) {
                echo Menu::widget([
                    'items' => $items,
                    'options' => [
                        'class' => 'inline-layout'
                    ]
                ]);
            }
            ?>
        </nav>
        <div class="block_phone">
            <a href="tel:<?= $model->getSetting('telephone') ?>"><?= $model->getSetting('telephone') ?></a>
            <a href="tel:<?= $model->getSetting('telephone2') ?>"><?= $model->getSetting('telephone2') ?></a>
            <a href="tel:<?= $model->getSetting('telephone3') ?>"><?= $model->getSetting('telephone3') ?></a>
        </div>
        <div class="block_button">
            <a href="#<?= $model->getSetting('form1Link') ?>" class="button open_modal"><?= $model->getSetting('form1') ?></a>
            <a href="#<?= $model->getSetting('form2Link') ?>" class="button open_modal"><?= $model->getSetting('form2') ?></a>
        </div>
        <div class="block_lang">
            <a class="<?= (Yii::$app->language == 'ru-RU') ? 'active':'' ?>"
               href="<?= Url::toRoute(['/', '__language' => 'ru']) ?>">РУС</a>
            <a class="<?= (Yii::$app->language == 'uk-UA') ? 'active':'' ?>"
               href="<?= Url::toRoute(['/', '__language' => 'ua']) ?>">УКР</a>
        </div>
        <div class="menu-icon-open">
            <span></span>
        </div>
        <div class="menu_mobile">
            <div class="menu-icon-close">
                <span></span>
            </div>
            <div class="menu-holder">
                <nav>
                    <?php
                    $items = ApiMenu::getMenuLvl($model->getSetting('menu'), 0, 1);
                    echo Menu::widget([
                        'items' => $items,
                        'options' => [
                            'class' => 'inline-layout'
                        ]
                    ]);
                    ?>
                </nav>
                <div class="block_lang">
                    <a class="<?= (Yii::$app->language == 'ru-RU') ? 'active':'' ?>"
                       href="<?= Url::toRoute(['/', '__language' => 'ru']) ?>">РУС</a>
                    <a class="<?= (Yii::$app->language == 'uk-UA') ? 'active':'' ?>"
                       href="<?= Url::toRoute(['/', '__language' => 'ua']) ?>">УКР</a>
                </div>
            </div>
        </div>
    </div>
    <div class="text_header">
        <div class="block_text">
            <div>
                <h2 data-wow-delay="0.5s" class="wow fancy_title"><?= $model->getSetting('title') ?></h2>
                <p data-wow-delay="1s" class="wow fancy_title2"><?= $model->getSetting('description') ?></p>
                <a href="#<?= $model->getSetting('formCenterLink') ?>" class="button open_modal"><?= $model->getSetting('formCenter') ?></a>
            </div>
        </div>
        <a data-wow-delay="2.5s" href="#block_vacancy" class="button_down wow fadeIn">
            <span></span>
            <span></span>
            <span></span>
        </a>
    </div>
</header>
