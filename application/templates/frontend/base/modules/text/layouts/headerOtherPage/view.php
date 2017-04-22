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

<header<?php echo isset($this->params['background']) ? ' style="background: url(\''. $this->params['background'] .'\') no-repeat center;background-size: cover;"' : '' ?>>
    <div class="container">
        <div class="logo">
            <a <?= $isHome ? '' : 'href="'.\yii\helpers\Url::to(['/']).'"' ?> ><?= $model->getSetting('logoTitle') ?>
                <span><?= $model->getSetting('logoDescription') ?></span>
            </a>
        </div>
        <nav>
            <?php
            $items = ApiMenu::getMenuLvl($model->getSetting('menu'), 1, 1);
            array_splice($items, 5);
            echo Menu::widget([
                'items' => $items,
                'options' => [
                    'class' => 'inline-layout'
                ]
            ]);
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
</header>
