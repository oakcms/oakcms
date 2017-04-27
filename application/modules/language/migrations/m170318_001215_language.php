<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
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
            'PRIMARY KEY (`language_id`)'
        ], $tableOptions);

        $this->createIndex('url', '{{%language}}', 'url', false);

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
    }

    public function safeDown()
    {
        $this->dropTable('{{%language}}');
    }
}
