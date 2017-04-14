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

<footer>
    <div class="container">
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
        <div class="logo">
            <a <?= $isHome ? '' : 'href="'.\yii\helpers\Url::home().'"' ?> ><?= $model->getSetting('logoTitle') ?>
                <span><?= $model->getSetting('logoDescription') ?></span>
            </a>
        </div>
        <div class="block_contact inline-layout">
            <div class="item">
                <i class="fa fa-map-marker"></i>
                <div class="address">
                    <span><?= $model->getSetting('country1') ?>:</span>
                    <address><?= $model->getSetting('address1') ?></address>
                    <a href="tel:<?= $model->getSetting('telephone1_1') ?>"><?= $model->getSetting('telephone1_1') ?></a>
                    <a href="tel:<?= $model->getSetting('telephone1_2') ?>"><?= $model->getSetting('telephone1_2') ?></a>
                    <a href="tel:<?= $model->getSetting('telephone1_3') ?>"><?= $model->getSetting('telephone1_3') ?></a>
                </div>
            </div>
            <div class="item"><i class="fa fa-map-marker"></i>
                <div class="address">
                    <span><?= $model->getSetting('country2') ?>:</span>
                    <address><?= $model->getSetting('address2') ?></address>
                    <a href="tel:<?= $model->getSetting('telephone2_1') ?>"><?= $model->getSetting('telephone2_1') ?></a>
                </div>
            </div>
            <div class="item social_link"><span>МЫ В СОЦСЕТЯХ</span>
                <ul class="inline-layout">
                    <li><a href="<?= $model->getSetting('vkLink') ?>" target="_blank"><i class="fa fa-vk"></i></a></li>
                    <li><a href="<?= $model->getSetting('gpluseLink') ?>" target="_blank"><i class="fa fa-google-plus"></i></a></li>
                    <li><a href="<?= $model->getSetting('facebookLink') ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="<?= $model->getSetting('odLink') ?>" target="_blank"><i class="fa fa-odnoklassniki"></i></a></li>
                    <li><a href="<?= $model->getSetting('youtubePlayLink') ?>" target="_blank"><i class="fa fa-youtube-play"></i></a></li>
                    <li><a href="<?= $model->getSetting('twitterLink') ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
