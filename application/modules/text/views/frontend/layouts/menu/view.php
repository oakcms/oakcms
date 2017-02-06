<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 *
 * @var $model \app\modules\text\models\Text;
 */

use app\modules\menu\api\Menu;

$subMenu = Menu::getMenuLvl($model->getSetting('menu_type_id'), $model->getSetting('start_lvl'), $model->getSetting('end_lvl'), $model->getSetting('menu_item_id'));
echo \app\modules\system\components\Menu::widget([
    'items' => $subMenu
]);

