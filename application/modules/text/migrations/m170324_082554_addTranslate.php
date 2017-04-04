<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\text\migrations;

use app\components\Migration;

class m170324_082554_addTranslate extends Migration
{

    public function init()
    {
        $this->db = 'db';

        $this->translations = [
            'ru-RU' => [
                'text' => [
                    'On all pages'                       => 'На всех страницах',
                    'Not on the same page'               => 'Ни на одной странице',
                    'On these pages only'                => 'Только на указаных страницах',
                    'On all pages, except for the above' => 'На всех страницах, кроме выбраных',
                    'Binding to the menu'                => 'Привязка к меню',
                    'Where To Place'                     => 'Где поместить',
                    'Menus'                              => 'Меню',
                    'Menu where will be displayed'       => 'Меню где будет выведено',
                ],
            ],
        ];

        parent::init();
    }

    public function safeUp()
    {
        $this->upDbTranslate();
    }

    public function safeDown()
    {
        $this->downDbTranslate();
    }
}
