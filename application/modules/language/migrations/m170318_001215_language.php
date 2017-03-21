<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\language\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170318_001215_language extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable('{{%language}}', [
            'language_id' => $this->string(5),
            'language'    => $this->string(3)->notNull(),
            'country'     => $this->string(3)->notNull(),
            'url'         => $this->string(3)->notNull(),
            'name'        => $this->string(32)->notNull(),
            'name_ascii'  => $this->string(32)->notNull(),
            'status'      => $this->smallInteger(6)->notNull(),
        ], $tableOptions);
        $this->createIndex('url', '{{%language}}', 'url', false);
        $this->addPrimaryKey('language_id', '{{%language}}', 'language_id');

        $this->createTable('{{%language_source}}', [
            'id'       => $this->primaryKey(11),
            'category' => $this->string(32)->null()->defaultValue(null),
            'message'  => $this->text()->null()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%language_translate}}', [
            'id'          => $this->primaryKey(11),
            'language'    => $this->string(16)->notNull(),
            'translation' => $this->text()->null()->defaultValue(null),
        ], $tableOptions);

        $this->batchInsert('{{%language}}',
            ["language_id", "language", "country", "url", "name", "name_ascii", "status"],
            [
                [
                    'language_id' => 'be-BY',
                    'language'    => 'be',
                    'country'     => 'by',
                    'url'         => 'by',
                    'name'        => 'Беларуская',
                    'name_ascii'  => 'Belarusian',
                    'status'      => '0',
                ],
                [
                    'language_id' => 'en-US',
                    'language'    => 'en',
                    'country'     => 'us',
                    'url'         => 'en',
                    'name'        => 'English (US)',
                    'name_ascii'  => 'English (US)',
                    'status'      => '0',
                ],
                [
                    'language_id' => 'ru-RU',
                    'language'    => 'ru',
                    'country'     => 'ru',
                    'url'         => 'ru',
                    'name'        => 'Русский',
                    'name_ascii'  => 'Russian',
                    'status'      => '1',
                ],
                [
                    'language_id' => 'uk-UA',
                    'language'    => 'uk',
                    'country'     => 'ua',
                    'url'         => 'ua',
                    'name'        => 'Українська',
                    'name_ascii'  => 'Ukrainian',
                    'status'      => '0',
                ],
            ]
        );
        $this->batchInsert('{{%language_source}}',
            ["id", "category", "message"],
            [
                [
                    'id'       => '1',
                    'category' => 'admin',
                    'message'  => 'Translatable element',
                ],
                [
                    'id'       => '2',
                    'category' => 'admin',
                    'message'  => 'Create',
                ],
                [
                    'id'       => '3',
                    'category' => 'admin',
                    'message'  => 'Update',
                ],
                [
                    'id'       => '4',
                    'category' => 'admin',
                    'message'  => 'Save & Continue Edit',
                ],
                [
                    'id'       => '5',
                    'category' => 'admin',
                    'message'  => 'Control',
                ],
                [
                    'id'       => '6',
                    'category' => 'admin',
                    'message'  => 'Back',
                ],
                [
                    'id'       => '7',
                    'category' => 'admin',
                    'message'  => 'System',
                ],
                [
                    'id'       => '8',
                    'category' => 'admin',
                    'message'  => 'Settings',
                ],
                [
                    'id'       => '9',
                    'category' => 'admin',
                    'message'  => 'Modules',
                ],
                [
                    'id'       => '10',
                    'category' => 'admin',
                    'message'  => 'Cache',
                ],
                [
                    'id'       => '11',
                    'category' => 'admin',
                    'message'  => 'File Manager',
                ],
                [
                    'id'       => '12',
                    'category' => 'admin',
                    'message'  => 'Flush cache',
                ],
                [
                    'id'       => '13',
                    'category' => 'admin',
                    'message'  => 'Clear assets',
                ],
                [
                    'id'       => '14',
                    'category' => 'admin',
                    'message'  => 'Main navigation',
                ],
                [
                    'id'       => '15',
                    'category' => 'admin',
                    'message'  => 'Delete',
                ],
                [
                    'id'       => '16',
                    'category' => 'language',
                    'message'  => 'Language',
                ],
                [
                    'id'       => '17',
                    'category' => 'language',
                    'message'  => 'Country',
                ],
                [
                    'id'       => '18',
                    'category' => 'language',
                    'message'  => 'Translation',
                ],
                [
                    'id'       => '19',
                    'category' => 'language',
                    'message'  => 'Text',
                ],
                [
                    'id'       => '20',
                    'category' => 'language',
                    'message'  => 'Multilingual',
                ],
                [
                    'id'       => '21',
                    'category' => 'language',
                    'message'  => 'Category',
                ],
                [
                    'id'       => '22',
                    'category' => 'admin',
                    'message'  => 'Live edit',
                ],
                [
                    'id'       => '23',
                    'category' => 'admin',
                    'message'  => 'Admin panel',
                ],
                [
                    'id'       => '24',
                    'category' => 'shop',
                    'message'  => 'Shop',
                ],
                [
                    'id'       => '25',
                    'category' => 'field',
                    'message'  => 'Fields',
                ],
                [
                    'id'       => '26',
                    'category' => 'filter',
                    'message'  => 'Filter',
                ],
                [
                    'id'       => '27',
                    'category' => 'user',
                    'message'  => 'Users',
                ],
                [
                    'id'       => '28',
                    'category' => 'admin',
                    'message'  => 'Save & Create new',
                ],
                [
                    'id'       => '29',
                    'category' => 'text',
                    'message'  => 'Html Blocks',
                ],
            ]
        );
        $this->batchInsert('{{%language_translate}}',
            ["id", "language", "translation"],
            [
                [
                    'id'          => '1',
                    'language'    => 'ru-RU',
                    'translation' => 'Переводимое поле',
                ],
                [
                    'id'          => '2',
                    'language'    => 'ru-RU',
                    'translation' => 'Создать',
                ],
                [
                    'id'          => '3',
                    'language'    => 'ru-RU',
                    'translation' => 'Обновить',
                ],
                [
                    'id'          => '4',
                    'language'    => 'ru-RU',
                    'translation' => 'Сохранить и вернуться',
                ],
                [
                    'id'          => '5',
                    'language'    => 'ru-RU',
                    'translation' => 'Контроль',
                ],
                [
                    'id'          => '6',
                    'language'    => 'ru-RU',
                    'translation' => 'Назад',
                ],
                [
                    'id'          => '7',
                    'language'    => 'ru-RU',
                    'translation' => 'Система',
                ],
                [
                    'id'          => '8',
                    'language'    => 'ru-RU',
                    'translation' => 'Настройки',
                ],
                [
                    'id'          => '9',
                    'language'    => 'ru-RU',
                    'translation' => 'Модули',
                ],
                [
                    'id'          => '10',
                    'language'    => 'ru-RU',
                    'translation' => 'Кеш',
                ],
                [
                    'id'          => '11',
                    'language'    => 'ru-RU',
                    'translation' => 'Файловый менеджер',
                ],
                [
                    'id'          => '12',
                    'language'    => 'ru-RU',
                    'translation' => 'Очистить кеш',
                ],
                [
                    'id'          => '13',
                    'language'    => 'ru-RU',
                    'translation' => 'Очистить активы',
                ],
                [
                    'id'          => '14',
                    'language'    => 'ru-RU',
                    'translation' => 'Главное меню',
                ],
                [
                    'id'          => '15',
                    'language'    => 'ru-RU',
                    'translation' => 'Удалить',
                ],
                [
                    'id'          => '16',
                    'language'    => 'ru-RU',
                    'translation' => 'Язык',
                ],
                [
                    'id'          => '17',
                    'language'    => 'ru-RU',
                    'translation' => 'Страна',
                ],
                [
                    'id'          => '18',
                    'language'    => 'ru-RU',
                    'translation' => 'Перевод',
                ],
                [
                    'id'          => '19',
                    'language'    => 'ru-RU',
                    'translation' => 'Текст',
                ],
                [
                    'id'          => '20',
                    'language'    => 'ru-RU',
                    'translation' => 'Мультиязычность',
                ],
                [
                    'id'          => '21',
                    'language'    => 'ru-RU',
                    'translation' => 'Категория',
                ],
                [
                    'id'          => '22',
                    'language'    => 'ru-RU',
                    'translation' => 'Жывое редактирование',
                ],
                [
                    'id'          => '23',
                    'language'    => 'ru-RU',
                    'translation' => 'Администрирование',
                ],
                [
                    'id'          => '24',
                    'language'    => 'ru-RU',
                    'translation' => 'Магазин',
                ],
                [
                    'id'          => '25',
                    'language'    => 'ru-RU',
                    'translation' => 'Дополнительные поля',
                ],
                [
                    'id'          => '26',
                    'language'    => 'ru-RU',
                    'translation' => 'Фильтр',
                ],
                [
                    'id'          => '27',
                    'language'    => 'ru-RU',
                    'translation' => 'Пользователи',
                ],
                [
                    'id'          => '28',
                    'language'    => 'ru-RU',
                    'translation' => 'Сохранить и создать',
                ],
                [
                    'id'          => '29',
                    'language'    => 'ru-RU',
                    'translation' => 'Html блоки',
                ],
            ]
        );
    }

    public function safeDown()
    {

        $this->dropTable('{{%language}}');
        $this->dropTable('{{%language_source}}');
        $this->dropTable('{{%language_translate}}');

    }
}
